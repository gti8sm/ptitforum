<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Notifications\MentionedInThreadNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $groups = Group::query()
            ->whereHas('memberships', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['createdBy'])
            ->withCount(['threads'])
            ->orderBy('name')
            ->get();

        $groupIds = $groups->pluck('id')->all();

        $unreadPostsCountByGroupId = collect();

        if (! empty($groupIds)) {
            $unreadPostsCountByGroupId = DB::table('threads')
                ->join('posts', function ($join) {
                    $join->on('posts.thread_id', '=', 'threads.id')
                        ->whereNull('posts.deleted_at');
                })
                ->leftJoin('thread_reads', function ($join) use ($user) {
                    $join->on('thread_reads.thread_id', '=', 'threads.id')
                        ->where('thread_reads.user_id', '=', $user->id);
                })
                ->whereIn('threads.group_id', $groupIds)
                ->whereNull('threads.deleted_at')
                ->where('posts.user_id', '!=', $user->id)
                ->whereRaw('posts.id > COALESCE(thread_reads.last_read_post_id, 0)')
                ->groupBy('threads.group_id')
                ->select('threads.group_id', DB::raw('COUNT(posts.id) as unread_posts_count'))
                ->pluck('unread_posts_count', 'group_id');
        }

        $membershipByGroupId = $user
            ->memberships()
            ->get()
            ->keyBy('group_id');

        $unreadMentionsCountByGroupId = $user
            ->unreadNotifications()
            ->where('type', MentionedInThreadNotification::class)
            ->get()
            ->groupBy(fn ($n) => (int) ($n->data['group_id'] ?? 0))
            ->map(fn ($items) => $items->count());

        return view('dashboard', [
            'groups' => $groups,
            'membershipByGroupId' => $membershipByGroupId,
            'unreadPostsCountByGroupId' => $unreadPostsCountByGroupId,
            'unreadMentionsCountByGroupId' => $unreadMentionsCountByGroupId,
        ]);
    }
}
