<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Merci pour ton inscription ! Avant de commencer, peux-tu vérifier ton adresse e-mail en cliquant sur le lien que nous venons de t’envoyer ? Si tu n’as rien reçu, nous pouvons te renvoyer un email.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Un nouveau lien de vérification a été envoyé à l’adresse e-mail utilisée lors de l’inscription.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Renvoyer l’e-mail de vérification') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                {{ __('Se déconnecter') }}
            </button>
        </form>
    </div>
</x-guest-layout>
