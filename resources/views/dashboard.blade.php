<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-sm text-gray-600">Bienvenue</div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight tracking-tight">
                    {{ auth()->user()->name }}
                </h2>
            </div>

            @if(auth()->user()?->role === 'admin')
                <a href="/admin" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition shadow-sm">
                    Administration
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-600">Tes espaces</div>
                            <div class="mt-1 text-lg font-semibold text-gray-900">Groupes</div>
                        </div>

                        @if(auth()->user()?->role === 'admin')
                            <a href="/admin/groups" class="shrink-0 inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                                Gérer les groupes
                            </a>
                        @endif
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($groups as $group)
                            <a href="{{ route('groups.show', $group) }}" class="group block overflow-hidden rounded-2xl bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 truncate">{{ $group->name }}</div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                {{ $group->threads_count }} sujet{{ $group->threads_count > 1 ? 's' : '' }}
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end gap-2">
                                            @php($membership = $membershipByGroupId[$group->id] ?? null)
                                            @php($unreadPostsCount = (int) ($unreadPostsCountByGroupId[$group->id] ?? 0))
                                            @php($unreadMentionsCount = (int) ($unreadMentionsCountByGroupId[$group->id] ?? 0))
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
                                            @if($membership)
                                                <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-2.5 py-1 text-xs font-semibold">
                                                    {{ $membership->role }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($group->description)
                                        <div class="mt-3 text-sm text-gray-700 line-clamp-3">
                                            {{ $group->description }}
                                        </div>
                                    @endif

                                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                                        <div>
                                            Privé : {{ $group->is_private ? 'oui' : 'non' }}
                                        </div>
                                        <div class="font-semibold text-gray-700 group-hover:text-gray-900 transition">Entrer</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="sm:col-span-2 lg:col-span-3 rounded-2xl bg-white border border-gray-200 p-6 text-gray-700 shadow-sm">
                                Tu n’es membre d’aucun groupe pour le moment.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if(auth()->user()?->role === 'admin')
                <div class="mt-6 overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <div class="text-sm text-gray-600">Admin</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">Raccourcis</div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <a href="/admin/threads" class="block rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50 transition shadow-sm">
                                <div class="font-semibold text-gray-900">Créer des sujets</div>
                                <div class="text-sm text-gray-600">Discussions + sondages</div>
                            </a>
                            <a href="/admin/invitations" class="block rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50 transition shadow-sm">
                                <div class="font-semibold text-gray-900">Inviter des membres</div>
                                <div class="text-sm text-gray-600">Envoi d’email automatique</div>
                            </a>
                            <a href="/admin/users" class="block rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50 transition shadow-sm">
                                <div class="font-semibold text-gray-900">Utilisateurs</div>
                                <div class="text-sm text-gray-600">Rôles, droits</div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
