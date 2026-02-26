<x-guest-layout>
    <div class="w-full">
        <div class="text-center">
            <div class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">
                Forum privé • Sur invitation
            </div>

            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900">P'tit Forum</h1>
            <p class="mt-3 text-gray-700">Un espace simple pour organiser les discussions de ta troupe : décor, costumes, répétitions, planning, idées…</p>
        </div>

        <div class="mt-8 grid gap-4">
            <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-gray-900 border border-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition shadow-sm">
                Se connecter
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-sm text-gray-900 hover:bg-gray-50 transition shadow-sm">
                    Créer un compte (sur invitation)
                </a>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="font-semibold text-gray-900">Inscription</div>
                <div class="mt-1 text-sm text-gray-700">Le forum fonctionne sur invitation. Si tu as reçu un email, clique sur le lien pour créer ton compte.</div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl bg-white p-4 border border-gray-200 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Groupes</div>
                    <div class="mt-1 text-xs text-gray-700">Décor, costumes, technique…</div>
                </div>
                <div class="rounded-2xl bg-white p-4 border border-gray-200 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Sujets</div>
                    <div class="mt-1 text-xs text-gray-700">Discussions + décisions</div>
                </div>
                <div class="rounded-2xl bg-white p-4 border border-gray-200 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Sondages</div>
                    <div class="mt-1 text-xs text-gray-700">Trancher rapidement</div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
