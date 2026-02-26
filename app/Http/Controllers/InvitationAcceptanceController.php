<?php

namespace App\Http\Controllers;

use App\Models\GroupMembership;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class InvitationAcceptanceController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_if($invitation->accepted_at !== null, 410);
        abort_if($invitation->expires_at !== null && $invitation->expires_at->isPast(), 410);

        return view('invitations.accept', [
            'invitation' => $invitation,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_if($invitation->accepted_at !== null, 410);
        abort_if($invitation->expires_at !== null && $invitation->expires_at->isPast(), 410);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => $invitation->email],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
            ],
        );

        if (! $user->wasRecentlyCreated) {
            // If the user already exists, just update the name if it was empty.
            if (! $user->name) {
                $user->forceFill(['name' => $validated['name']])->save();
            }
        } else {
            event(new Registered($user));
        }

        if ($invitation->group_id) {
            GroupMembership::query()->firstOrCreate(
                [
                    'group_id' => $invitation->group_id,
                    'user_id' => $user->id,
                ],
                [
                    'role' => 'member',
                    'joined_at' => now(),
                ],
            );
        }

        $invitation->forceFill([
            'accepted_at' => now(),
        ])->save();

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
