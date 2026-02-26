<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ThreadRead;
use App\Notifications\MentionedInThreadNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ThreadController extends Controller
{
    public function create(Request $request, Group $group): View
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canModerate = $user->role === 'admin' || $group->isModerator($user);
        abort_unless($canModerate, 403);

        return view('threads.create', [
            'group' => $group,
        ]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canModerate = $user->role === 'admin' || $group->isModerator($user);
        abort_unless($canModerate, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
        ]);

        $thread = Thread::query()->create([
            'group_id' => $group->id,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'is_pinned' => false,
            'is_locked' => false,
            'last_activity_at' => now(),
        ]);

        return redirect()->route('threads.show', [$group, $thread]);
    }

    public function show(Request $request, Group $group, Thread $thread): View
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canModerate = $user->role === 'admin' || $group->isModerator($user);

        $thread->loadMissing(['creator']);

        $poll = $thread
            ->poll()
            ->with(['options', 'votes'])
            ->first();

        $posts = $thread
            ->posts()
            ->with(['author'])
            ->orderBy('created_at')
            ->get();

        $lastPostId = $posts->last()?->id;

        if ($lastPostId) {
            ThreadRead::query()->updateOrCreate(
                [
                    'thread_id' => $thread->id,
                    'user_id' => $user->id,
                ],
                [
                    'last_read_post_id' => $lastPostId,
                    'last_read_at' => now(),
                ],
            );
        }

        $mentionNotifications = $user
            ->unreadNotifications()
            ->where('type', MentionedInThreadNotification::class)
            ->get()
            ->filter(function ($notification) use ($group, $thread) {
                return (int) ($notification->data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($notification->data['thread_id'] ?? 0) === (int) $thread->id;
            });

        if ($mentionNotifications->isNotEmpty()) {
            $mentionNotifications->markAsRead();
        }

        return view('threads.show', [
            'group' => $group,
            'thread' => $thread,
            'posts' => $posts,
            'canModerate' => $canModerate,
            'poll' => $poll,
        ]);
    }

    public function storePost(Request $request, Group $group, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        abort_if($thread->is_locked, 423);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['file', 'image', 'max:4096'],
        ]);

        $attachments = [];

        foreach (($validated['images'] ?? []) as $file) {
            $path = $file->store('posts', 'public');
            $attachments[] = [
                'type' => 'image',
                'disk' => 'public',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'name' => $file->getClientOriginalName(),
            ];
        }

        $post = Post::query()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
            'attachments' => empty($attachments) ? null : $attachments,
        ]);

        preg_match_all('/(^|\s)@([\p{L}0-9_\.-]{2,64})/u', $post->body, $matches);
        $mentionedNames = collect($matches[2] ?? [])
            ->map(fn ($name) => trim($name))
            ->filter()
            ->map(fn ($name) => mb_strtolower($name))
            ->unique()
            ->values();

        if ($mentionedNames->isNotEmpty()) {
            $mentionedUsers = $group
                ->members()
                ->where('users.id', '!=', $user->id)
                ->get(['users.id', 'users.name'])
                ->filter(function ($member) use ($mentionedNames) {
                    return $mentionedNames->contains(mb_strtolower($member->name));
                });

            foreach ($mentionedUsers as $mentionedUser) {
                $mentionedUser->notify(new MentionedInThreadNotification($group, $thread, $user));
            }
        }

        $lastPostId = Post::query()
            ->where('thread_id', $thread->id)
            ->latest('id')
            ->value('id');

        if ($lastPostId) {
            ThreadRead::query()->updateOrCreate(
                [
                    'thread_id' => $thread->id,
                    'user_id' => $user->id,
                ],
                [
                    'last_read_post_id' => $lastPostId,
                    'last_read_at' => now(),
                ],
            );
        }

        $thread->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return redirect()
            ->route('threads.show', [$group, $thread])
            ->with('status', 'post-created');
    }

    public function togglePin(Request $request, Group $group, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $thread->forceFill([
            'is_pinned' => ! $thread->is_pinned,
        ])->save();

        return redirect()->route('threads.show', [$group, $thread]);
    }

    public function toggleLock(Request $request, Group $group, Thread $thread): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);
        abort_unless($user->role === 'admin' || $group->isModerator($user), 403);

        $thread->forceFill([
            'is_locked' => ! $thread->is_locked,
        ])->save();

        return redirect()->route('threads.show', [$group, $thread]);
    }

    public function destroyPost(Request $request, Group $group, Thread $thread, Post $post): RedirectResponse
    {
        $user = $request->user();

        abort_unless($thread->group_id === $group->id, 404);
        abort_unless($post->thread_id === $thread->id, 404);
        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canDelete = ($user->role === 'admin')
            || $group->isModerator($user)
            || ((int) $post->user_id === (int) $user->id);

        abort_unless($canDelete, 403);

        $post->delete();

        return redirect()->route('threads.show', [$group, $thread]);
    }
}
