<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Mes groupes</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('groups.show', $group) }}" class="hover:text-gray-900 transition">{{ $group->name }}</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">Planning</span>
                </div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 leading-tight tracking-tight">
                    Planning
                </h2>
            </div>

            @if($canManage)
                <div class="flex items-center gap-2">
                    <a href="{{ route('planning.create', $group) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg text-sm font-semibold text-white hover:bg-gray-800 transition">
                        Nouvel événement
                    </a>
                    <a href="/admin" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                        Gérer dans l’admin
                    </a>
                </div>
            @endif
        </div>

        <div class="mt-4 flex items-center gap-2">
            <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border transition bg-white border-gray-200 text-gray-900 hover:bg-gray-50">
                Sujets
            </a>
            <a href="{{ route('planning.index', $group) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border transition bg-gray-900 text-white border-gray-900">
                Planning
            </a>

            <div class="ms-auto flex items-center gap-2">
                <a href="{{ route('planning.index', $group) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold border transition
                    {{ request('view') === 'agenda' ? 'bg-white text-gray-900 border-gray-200 hover:bg-gray-50' : 'bg-gray-100 text-gray-900 border-gray-200' }}">
                    Cartes
                </a>
                <a href="{{ route('planning.index', $group) }}?view=agenda" class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold border transition
                    {{ request('view') === 'agenda' ? 'bg-gray-100 text-gray-900 border-gray-200' : 'bg-white text-gray-900 border-gray-200 hover:bg-gray-50' }}">
                    Agenda
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Groupe</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">{{ $group->name }}</div>
                        </div>
                        <div class="text-xs text-gray-500">{{ $events->count() }} événement{{ $events->count() > 1 ? 's' : '' }}</div>
                    </div>

                    @if(request('view') === 'agenda')
                        @php
                            $dated = $events->filter(fn($e) => !is_null($e->starts_at));
                            $undated = $events->filter(fn($e) => is_null($e->starts_at));
                            $byDay = $dated->groupBy(fn($e) => $e->starts_at->format('Y-m-d'));
                        @endphp

                        <div class="mt-6 grid gap-6">
                            @forelse($byDay as $day => $dayEvents)
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ \Illuminate\Support\Carbon::parse($day)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </div>

                                    <div class="mt-3 divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white">
                                        @foreach($dayEvents as $event)
                                            @php($my = $rsvpByEventId[$event->id] ?? null)
                                            <a href="{{ route('planning.show', [$group, $event]) }}" class="block px-4 py-3 hover:bg-gray-50 transition">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $event->title }}</div>
                                                        <div class="mt-1 text-sm text-gray-600">
                                                            {{ $event->starts_at->format('H:i') }}
                                                            @if($event->ends_at)
                                                                <span class="text-gray-400">→</span> {{ $event->ends_at->format('H:i') }}
                                                            @endif
                                                            @if($event->location)
                                                                <span class="text-gray-400">•</span> {{ $event->location }}
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                                            {{ $event->type }}
                                                        </span>

                                                        @if($my)
                                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                                {{ $my->status === 'yes' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $my->status === 'no' ? 'bg-red-100 text-red-800' : '' }}
                                                                {{ $my->status === 'maybe' ? 'bg-amber-100 text-amber-800' : '' }}
                                                            ">
                                                                {{ $my->status === 'yes' ? 'je viens' : '' }}
                                                                {{ $my->status === 'no' ? 'absent' : '' }}
                                                                {{ $my->status === 'maybe' ? 'peut-être' : '' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-700">
                                    Aucun événement daté pour le moment.
                                </div>
                            @endforelse

                            @if($undated->isNotEmpty())
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">À planifier</div>
                                    <div class="mt-3 divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white">
                                        @foreach($undated as $event)
                                            <a href="{{ route('planning.show', [$group, $event]) }}" class="block px-4 py-3 hover:bg-gray-50 transition">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900 truncate">{{ $event->title }}</div>
                                                        <div class="mt-1 text-sm text-gray-600">Date non définie</div>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                                        {{ $event->type }}
                                                    </span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @forelse($events as $event)
                                @php($my = $rsvpByEventId[$event->id] ?? null)
                                <a href="{{ route('planning.show', [$group, $event]) }}" class="group block overflow-hidden rounded-2xl bg-white border border-gray-200 hover:bg-gray-50 transition">
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 truncate">{{ $event->title }}</div>
                                                <div class="mt-1 text-sm text-gray-600">
                                                    {{ $event->starts_at ? $event->starts_at->format('d/m/Y H:i') : 'À planifier' }}
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-end gap-2">
                                                <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                                    {{ $event->type }}
                                                </span>

                                                @if($my)
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                        {{ $my->status === 'yes' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $my->status === 'no' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $my->status === 'maybe' ? 'bg-amber-100 text-amber-800' : '' }}
                                                    ">
                                                        {{ $my->status === 'yes' ? 'je viens' : '' }}
                                                        {{ $my->status === 'no' ? 'absent' : '' }}
                                                        {{ $my->status === 'maybe' ? 'peut-être' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($event->location)
                                            <div class="mt-3 text-sm text-gray-700">📍 {{ $event->location }}</div>
                                        @endif

                                        <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center gap-2">
                                                <span>✅ {{ $event->rsvps_yes_count }}</span>
                                                <span>❌ {{ $event->rsvps_no_count }}</span>
                                                <span>🤔 {{ $event->rsvps_maybe_count }}</span>
                                            </div>
                                            <div class="font-semibold text-gray-700 group-hover:text-gray-900 transition">Détails</div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="sm:col-span-2 lg:col-span-3 rounded-2xl bg-white border border-gray-200 p-6 text-gray-700">
                                    Aucun événement pour le moment.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
