<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">
            Invitation
        </div>
        <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-gray-900">Rejoindre le forum</h1>
        <p class="mt-2 text-sm text-gray-700">Crée ton compte en quelques secondes. Tu pourras ensuite accéder aux groupes et discussions.</p>
    </div>

    <form method="POST" action="{{ route('invitations.accept', ['token' => $invitation->token]) }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ $invitation->email }}" disabled />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="inline-flex justify-center items-center px-4 py-3 bg-gray-900 border border-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition shadow-sm">
                Créer mon compte
            </button>
        </div>
    </form>
</x-guest-layout>
