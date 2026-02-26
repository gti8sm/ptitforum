<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Groupes</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('groups.show', $group) }}" class="hover:text-gray-900 transition">{{ $group->name }}</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">Nouveau sujet</span>
                </div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 leading-tight tracking-tight">
                    Nouveau sujet
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="p-6">
                    <form method="POST" action="{{ route('threads.store', $group) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-900">Titre</label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" required />
                            @error('title')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="body" class="block text-sm font-semibold text-gray-900">Description (optionnel)</label>
                            <textarea id="body" name="body" rows="6" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" placeholder="Contexte, objectifs, questions…">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                                Annuler
                            </a>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg text-sm font-semibold text-white hover:bg-gray-800 transition">
                                Créer le sujet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
