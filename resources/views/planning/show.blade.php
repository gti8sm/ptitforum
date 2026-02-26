<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Groupes</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('groups.show', $group) }}" class="hover:text-gray-900 transition">{{ $group->name }}</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('planning.index', $group) }}" class="hover:text-gray-900 transition">Planning</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">Événement</span>
                </div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 leading-tight tracking-tight truncate">
                    {{ $event->title }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                    {{ $event->type }}
                </span>

                @if($canManage)
                    <a href="{{ route('planning.edit', [$group, $event]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('planning.destroy', [$group, $event]) }}" onsubmit="return confirm('Supprimer cet événement ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                            Supprimer
                        </button>
                    </form>

                    <a href="/admin" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                        Admin
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border transition bg-white border-gray-200 text-gray-900 hover:bg-gray-50">
                Sujets
            </a>
            <a href="{{ route('planning.index', $group) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold border transition bg-gray-900 text-white border-gray-900">
                Planning
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <div class="grid gap-2 text-sm text-gray-700">
                            <div><span class="text-gray-500">Quand :</span> {{ $event->starts_at ? $event->starts_at->format('d/m/Y H:i') : 'À planifier' }}</div>
                            @if($event->ends_at)
                                <div><span class="text-gray-500">Fin :</span> {{ $event->ends_at->format('d/m/Y H:i') }}</div>
                            @endif
                            @if($event->location)
                                <div><span class="text-gray-500">Où :</span> {{ $event->location }}</div>
                            @endif
                        </div>

                        @if($event->description)
                            <div class="mt-4 text-gray-800 whitespace-pre-line">{{ $event->description }}</div>
                        @endif

                        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4">
                            <div class="text-sm text-gray-600">Ta réponse</div>

                            <form method="POST" action="{{ route('planning.rsvp', [$group, $event]) }}" class="mt-3 grid gap-3">
                                @csrf

                                <div class="grid gap-2 sm:grid-cols-3">
                                    <button type="submit" name="status" value="yes" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-widest border transition
                                        {{ $myRsvp?->status === 'yes' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-900 border-gray-200 hover:bg-gray-50' }}
                                    ">Je viens</button>
                                    <button type="submit" name="status" value="no" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-widest border transition
                                        {{ $myRsvp?->status === 'no' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-900 border-gray-200 hover:bg-gray-50' }}
                                    ">Absent</button>
                                    <button type="submit" name="status" value="maybe" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-widest border transition
                                        {{ $myRsvp?->status === 'maybe' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-gray-900 border-gray-200 hover:bg-gray-50' }}
                                    ">Peut-être</button>
                                </div>

                                <div>
                                    <label for="comment" class="block text-sm font-semibold text-gray-900">Commentaire (optionnel)</label>
                                    <textarea id="comment" name="comment" rows="3" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" placeholder="Ex: j’arrive en retard / je peux aider au décor…">{{ old('comment', $myRsvp?->comment) }}</textarea>
                                    @error('comment')
                                        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                @error('status')
                                    <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>

                        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">Organisation</div>
                                    <div class="mt-1 text-lg font-semibold text-gray-900">Tâches</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $event->tasks->count() }} tâche{{ $event->tasks->count() > 1 ? 's' : '' }}</div>
                            </div>

                            <div class="mt-4 grid gap-2">
                                @forelse($event->tasks as $task)
                                    <div class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900">{{ $task->title }}</div>
                                                <div class="mt-1 text-xs text-gray-600">
                                                    @if($task->assignee)
                                                        Assignée à {{ $task->assignee->name }}
                                                    @endif
                                                    @if($task->due_at)
                                                        <span class="text-gray-400">•</span> échéance {{ $task->due_at->format('d/m/Y H:i') }}
                                                    @endif
                                                </div>
                                                @if($task->note)
                                                    <div class="mt-1 text-xs text-gray-600">{{ $task->note }}</div>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('planning.tasks.update-status', [$group, $event, $task]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-200 bg-white text-xs font-semibold">
                                                        <option value="todo" @selected($task->status === 'todo')>à faire</option>
                                                        <option value="doing" @selected($task->status === 'doing')>en cours</option>
                                                        <option value="done" @selected($task->status === 'done')>fait</option>
                                                    </select>
                                                </form>

                                                @if($canManage)
                                                    <form method="POST" action="{{ route('planning.tasks.destroy', [$group, $event, $task]) }}" onsubmit="return confirm('Supprimer cette tâche ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-900 hover:bg-gray-50 transition">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600">
                                        Aucune tâche pour le moment.
                                    </div>
                                @endforelse
                            </div>

                            @if($canManage)
                                <form method="POST" action="{{ route('planning.tasks.store', [$group, $event]) }}" class="mt-4 grid gap-3">
                                    @csrf
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label for="task_title" class="block text-sm font-semibold text-gray-900">Nouvelle tâche</label>
                                            <input id="task_title" name="title" type="text" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" placeholder="Ex: préparer la sono / apporter les costumes…" />
                                            @error('title')
                                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="due_at" class="block text-sm font-semibold text-gray-900">Échéance (optionnel)</label>
                                            <input id="due_at" name="due_at" type="datetime-local" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                            @error('due_at')
                                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="assigned_to" class="block text-sm font-semibold text-gray-900">Assignée à (ID utilisateur, optionnel)</label>
                                            <input id="assigned_to" name="assigned_to" type="number" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                            @error('assigned_to')
                                                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="note" class="block text-sm font-semibold text-gray-900">Note (optionnel)</label>
                                        <textarea id="note" name="note" rows="2" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400"></textarea>
                                        @error('note')
                                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg text-sm font-semibold text-white hover:bg-gray-800 transition">
                                            Ajouter
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <div class="text-sm text-gray-600">Présences</div>

                        <div class="mt-4 grid gap-2">
                            @foreach($event->rsvps as $r)
                                <div class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="font-semibold text-gray-900">{{ $r->user?->name ?? 'Membre' }}</div>
                                        <div class="text-xs font-semibold
                                            {{ $r->status === 'yes' ? 'text-green-700' : '' }}
                                            {{ $r->status === 'no' ? 'text-red-700' : '' }}
                                            {{ $r->status === 'maybe' ? 'text-amber-700' : '' }}
                                        ">
                                            {{ $r->status === 'yes' ? 'je viens' : '' }}
                                            {{ $r->status === 'no' ? 'absent' : '' }}
                                            {{ $r->status === 'maybe' ? 'peut-être' : '' }}
                                        </div>
                                    </div>
                                    @if($r->comment)
                                        <div class="mt-1 text-xs text-gray-600">{{ $r->comment }}</div>
                                    @endif
                                </div>
                            @endforeach

                            @if($event->rsvps->isEmpty())
                                <div class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600">
                                    Personne n’a encore répondu.
                                </div>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
