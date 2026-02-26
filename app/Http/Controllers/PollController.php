<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function store(Request $request, Group $group, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canModerate = $user->role === 'admin' || $group->isModerator($user);
        abort_unless($canModerate, 403);

        abort_if($thread->poll()->exists(), 409);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'is_multiple_choice' => ['nullable', 'boolean'],
            'closes_at' => ['nullable', 'date'],
            'options' => ['required', 'array', 'min:2', 'max:10'],
            'options.*' => ['required', 'string', 'max:120'],
        ]);

        $poll = Poll::query()->create([
            'thread_id' => $thread->id,
            'question' => $validated['question'],
            'is_multiple_choice' => (bool) ($validated['is_multiple_choice'] ?? false),
            'closes_at' => $validated['closes_at'] ?? null,
        ]);

        $sort = 0;
        foreach ($validated['options'] as $label) {
            $label = trim($label);
            if ($label === '') {
                continue;
            }

            PollOption::query()->create([
                'poll_id' => $poll->id,
                'label' => $label,
                'sort_order' => $sort,
            ]);

            $sort++;
        }

        if ($sort < 2) {
            $poll->delete();
            return back()->withErrors(['options' => 'Ajoute au moins 2 choix.']);
        }

        return redirect()->route('threads.show', [$group, $thread]);
    }

    public function vote(Request $request, Group $group, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $poll = $thread->poll()->with(['options'])->firstOrFail();

        abort_if($poll->isClosed(), 423);

        $validated = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'option_ids.*' => ['integer'],
            'option_id' => ['nullable', 'integer'],
        ]);

        $selectedOptionIds = collect($validated['option_ids'] ?? [])
            ->when(isset($validated['option_id']), function ($c) use ($validated) {
                return $c->push((int) $validated['option_id']);
            })
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedOptionIds->isEmpty()) {
            return back()->withErrors(['option_id' => 'Choisis au moins une option.']);
        }

        $allowedOptionIds = $poll->options->pluck('id')->map(fn ($id) => (int) $id)->values();

        $invalid = $selectedOptionIds->diff($allowedOptionIds);
        abort_if($invalid->isNotEmpty(), 422);

        if (! $poll->is_multiple_choice && $selectedOptionIds->count() > 1) {
            return back()->withErrors(['option_id' => 'Une seule option possible pour ce sondage.']);
        }

        PollVote::query()
            ->where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->delete();

        foreach ($selectedOptionIds as $optionId) {
            PollVote::query()->create([
                'poll_id' => $poll->id,
                'poll_option_id' => $optionId,
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('threads.show', [$group, $thread]);
    }
}
