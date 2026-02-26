<?php

namespace App\Http\Controllers;

use App\Models\EventRsvp;
use App\Models\EventTask;
use App\Models\Group;
use App\Models\GroupEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupPlanningController extends Controller
{
    public function create(Request $request, Group $group): View
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        return view('planning.create', [
            'group' => $group,
        ]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $event = GroupEvent::query()->create([
            'group_id' => $group->id,
            'created_by' => $user->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        return redirect()->route('planning.show', [$group, $event]);
    }

    public function index(Request $request, Group $group): View
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $events = $group
            ->events()
            ->withCount([
                'rsvps as rsvps_yes_count' => function ($query) {
                    $query->where('status', 'yes');
                },
                'rsvps as rsvps_no_count' => function ($query) {
                    $query->where('status', 'no');
                },
                'rsvps as rsvps_maybe_count' => function ($query) {
                    $query->where('status', 'maybe');
                },
            ])
            ->orderBy('starts_at')
            ->orderBy('created_at')
            ->get();

        $rsvpByEventId = EventRsvp::query()
            ->where('user_id', $user->id)
            ->whereIn('event_id', $events->pluck('id'))
            ->get()
            ->keyBy('event_id');

        $canManage = $user->role === 'admin' || $group->isModerator($user);

        return view('planning.index', [
            'group' => $group,
            'events' => $events,
            'rsvpByEventId' => $rsvpByEventId,
            'canManage' => $canManage,
        ]);
    }

    public function show(Request $request, Group $group, GroupEvent $event): View
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $event->loadMissing([
            'creator',
            'rsvps.user',
            'tasks.assignee',
        ]);

        $myRsvp = $event
            ->rsvps()
            ->where('user_id', $user->id)
            ->first();

        $canManage = $user->role === 'admin' || $group->isModerator($user);

        return view('planning.show', [
            'group' => $group,
            'event' => $event,
            'myRsvp' => $myRsvp,
            'canManage' => $canManage,
        ]);
    }

    public function storeTask(Request $request, Group $group, GroupEvent $event): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'integer'],
            'due_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        EventTask::query()->create([
            'event_id' => $event->id,
            'created_by' => $user->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => $validated['title'],
            'status' => 'todo',
            'due_at' => $validated['due_at'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('planning.show', [$group, $event]);
    }

    public function updateTaskStatus(Request $request, Group $group, GroupEvent $event, EventTask $task): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        abort_unless($task->event_id === $event->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        $task->forceFill([
            'status' => $validated['status'],
        ])->save();

        return redirect()->route('planning.show', [$group, $event]);
    }

    public function destroyTask(Request $request, Group $group, GroupEvent $event, EventTask $task): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        abort_unless($task->event_id === $event->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $task->delete();

        return redirect()->route('planning.show', [$group, $event]);
    }

    public function edit(Request $request, Group $group, GroupEvent $event): View
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        return view('planning.edit', [
            'group' => $group,
            'event' => $event,
        ]);
    }

    public function update(Request $request, Group $group, GroupEvent $event): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $event->forceFill([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ])->save();

        return redirect()->route('planning.show', [$group, $event]);
    }

    public function destroy(Request $request, Group $group, GroupEvent $event): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $event->delete();

        return redirect()->route('planning.index', [$group]);
    }

    public function rsvp(Request $request, Group $group, GroupEvent $event): RedirectResponse
    {
        $user = $request->user();

        abort_unless($event->group_id === $group->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:yes,no,maybe'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        EventRsvp::query()->updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ],
            [
                'status' => $validated['status'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()->route('planning.show', [$group, $event]);
    }
}
