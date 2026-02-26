<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Mes groupes</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('groups.show', $group) }}" class="hover:text-gray-900 transition">{{ $group->name }}</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('planning.index', $group) }}" class="hover:text-gray-900 transition">Planning</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">Modifier</span>
                </div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 leading-tight tracking-tight truncate">
                    Modifier : {{ $event->title }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="p-6">
                    <form method="POST" action="{{ route('planning.update', [$group, $event]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-900">Type</label>
                                <select id="type" name="type" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400">
                                    @foreach(['event' => 'Événement', 'repetition' => 'Répétition', 'rendezvous' => 'Rendez-vous', 'task' => 'Tâche'] as $k => $label)
                                        <option value="{{ $k }}" @selected(old('type', $event->type) === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-900">Titre</label>
                                <input id="title" name="title" type="text" value="{{ old('title', $event->title) }}" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                @error('title')
                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-900">Lieu (optionnel)</label>
                            <input id="location" name="location" type="text" value="{{ old('location', $event->location) }}" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                            @error('location')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="starts_at" class="block text-sm font-semibold text-gray-900">Début (optionnel)</label>
                                <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\\TH:i')) }}" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                @error('starts_at')
                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="ends_at" class="block text-sm font-semibold text-gray-900">Fin (optionnel)</label>
                                <input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', optional($event->ends_at)->format('Y-m-d\\TH:i')) }}" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                @error('ends_at')
                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-900">Description (optionnel)</label>
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('planning.show', [$group, $event]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                                Annuler
                            </a>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg text-sm font-semibold text-white hover:bg-gray-800 transition">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
