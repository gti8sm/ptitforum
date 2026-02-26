<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionedInThreadNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Group $group,
        public readonly Thread $thread,
        public readonly User $mentionedBy,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'mention',
            'group_id' => $this->group->id,
            'thread_id' => $this->thread->id,
            'title' => 'Mention dans un sujet',
            'body' => $this->mentionedBy->name.' t’a mentionné dans « '.$this->thread->title.' » ('.$this->group->name.').',
            'url' => route('threads.show', [$this->group, $this->thread]),
        ];
    }
}
