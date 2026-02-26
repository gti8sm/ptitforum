<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <div class="text-sm text-gray-600">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Mes groupes</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">{{ $group->name }}</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <h2 class="font-semibold text-xl text-gray-900 leading-tight tracking-tight">
                    {{ $group->name }}
                </h2>

                <div class="flex items-center gap-2">
                    <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border shadow-sm shadow-black/5 transition
                        {{ request()->routeIs('groups.show') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white border-gray-200 text-gray-900 hover:bg-gray-50' }}">
                        Sujets
                    </a>
                    <a href="{{ route('planning.index', $group) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border shadow-sm shadow-black/5 transition
                        {{ request()->routeIs('planning.*') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white border-gray-200 text-gray-900 hover:bg-gray-50' }}">
                        Planning
                    </a>

                    @if($canModerate)
                        <a href="/admin/threads" class="shrink-0 inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                            Gérer les sujets
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    @if($group->description)
                        <div class="mt-1 text-gray-700">{{ $group->description }}</div>
                    @else
                        <div class="mt-1 text-gray-600">Choisis un sujet ou crée-en un depuis l'administration.</div>
                    @endif

                    @if($canModerate)
                        <div class="mt-3">
                            <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                modération
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('planning.index', $group) }}" class="shrink-0 inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                        Planning
                    </a>

                    @if($canModerate)
                        <a href="/admin/threads" class="shrink-0 inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                            Gérer les sujets
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($threads as $thread)
                    <a href="{{ route('threads.show', [$group, $thread]) }}" class="group block overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $thread->title }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        @if($thread->creator)
                                            Par {{ $thread->creator->name }}
                                        @else
                                            
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    @php($unreadPostsCount = (int) ($unreadPostsCountByThreadId[$thread->id] ?? 0))
                                    @php($unreadMentionsCount = (int) ($unreadMentionsCountByThreadId[$thread->id] ?? 0))
                                    @if($unreadPostsCount > 0)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-2.5 py-1 text-xs font-semibold">
                                            {{ $unreadPostsCount }} non lu{{ $unreadPostsCount > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    @if($unreadMentionsCount > 0)
                                        <span class="inline-flex items-center rounded-full bg-violet-100 text-violet-800 px-2.5 py-1 text-xs font-semibold">
                                            {{ $unreadMentionsCount }} mention{{ $unreadMentionsCount > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    @if($thread->is_pinned)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-2.5 py-1 text-xs font-semibold">
                                            pinglée
                                        </span>
                                    @endif

                                    @if($thread->is_locked)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                            verrouillé
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($thread->body)
                                <div class="mt-3 text-sm text-gray-700 line-clamp-3">
                                    {{ $thread->body }}
                                </div>
                            @endif

                            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                                <div>
                                    Activité :
                                    {{ optional($thread->last_activity_at)->diffForHumans() ?? $thread->created_at->diffForHumans() }}
                                </div>
                                <div class="font-semibold text-gray-700 group-hover:text-gray-900 transition">Ouvrir</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                        <div class="p-6 text-gray-700">
                            Aucun sujet pour le moment.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
