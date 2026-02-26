<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Notifications\MentionedInThreadNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function show(Request $request, Group $group): View
    {
        $user = $request->user();

        $isAllowed = $user->role === 'admin' || $group->isMember($user);
        abort_unless($isAllowed, 403);

        $canModerate = $user->role === 'admin' || $group->isModerator($user);

        $threads = $group
            ->threads()
            ->with(['creator'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_activity_at')
            ->orderByDesc('created_at')
            ->get();

        $threadIds = $threads->pluck('id')->all();

        $unreadPostsCountByThreadId = collect();

        if (! empty($threadIds)) {
            $unreadPostsCountByThreadId = DB::table('threads')
                ->join('posts', function ($join) {
                    $join->on('posts.thread_id', '=', 'threads.id')
                        ->whereNull('posts.deleted_at');
                })
                ->leftJoin('thread_reads', function ($join) use ($user) {
                    $join->on('thread_reads.thread_id', '=', 'threads.id')
                        ->where('thread_reads.user_id', '=', $user->id);
                })
                ->whereIn('threads.id', $threadIds)
                ->whereNull('threads.deleted_at')
                ->where('posts.user_id', '!=', $user->id)
                ->whereRaw('posts.id > COALESCE(thread_reads.last_read_post_id, 0)')
                ->groupBy('threads.id')
                ->select('threads.id', DB::raw('COUNT(posts.id) as unread_posts_count'))
                ->pluck('unread_posts_count', 'id');
        }

        $unreadMentionsCountByThreadId = $user
            ->unreadNotifications()
            ->where('type', MentionedInThreadNotification::class)
            ->get()
            ->filter(fn ($n) => (int) ($n->data['group_id'] ?? 0) === (int) $group->id)
            ->groupBy(fn ($n) => (int) ($n->data['thread_id'] ?? 0))
            ->map(fn ($items) => $items->count());

        return view('groups.show', [
            'group' => $group,
            'threads' => $threads,
            'canModerate' => $canModerate,
            'unreadPostsCountByThreadId' => $unreadPostsCountByThreadId,
            'unreadMentionsCountByThreadId' => $unreadMentionsCountByThreadId,
        ]);
    }
}
