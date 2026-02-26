<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Mail\InvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] = Str::random(40);
        $data['invited_by'] = auth()->id();

        if (! array_key_exists('expires_at', $data) || $data['expires_at'] === null) {
            $data['expires_at'] = now()->addDays(7);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\Invitation $invitation */
        $invitation = $this->record;

        Mail::to($invitation->email)->send(new InvitationEmail($invitation));
    }
}
