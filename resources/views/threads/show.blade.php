<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition">Groupes</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('groups.show', $group) }}" class="hover:text-gray-900 transition">{{ $group->name }}</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-600">Sujet</span>
                </div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 leading-tight tracking-tight truncate">
                    {{ $thread->title }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
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

                @if($canModerate)
                    <form method="POST" action="{{ route('threads.toggle-pin', [$group, $thread]) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-900 hover:bg-gray-50 transition">
                            {{ $thread->is_pinned ? 'Dépingle' : 'Épingle' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('threads.toggle-lock', [$group, $thread]) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-900 hover:bg-gray-50 transition">
                            {{ $thread->is_locked ? 'Déverrouille' : 'Verrouille' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <a href="{{ route('groups.show', $group) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border shadow-sm shadow-black/5 transition bg-gray-900 text-white border-gray-900">
                Sujets
            </a>
            <a href="{{ route('planning.index', $group) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border transition bg-white border-gray-200 text-gray-900 hover:bg-gray-50">
                Planning
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                    <div class="p-6">
                        @if($thread->body)
                            <div class="text-gray-800 whitespace-pre-line">{{ $thread->body }}</div>
                        @else
                            <div class="text-gray-600">Aucune description pour ce sujet.</div>
                        @endif

                        <div class="mt-8">
                            <div class="text-sm text-gray-600">Sondage</div>

                            @if($poll)
                                @php($totalVotes = (int) $poll->votes->count())
                                @php($myVoteOptionIds = $poll->votes->where('user_id', auth()->id())->pluck('poll_option_id')->map(fn ($id) => (int) $id)->all())

                                <div class="mt-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                                    <div class="font-semibold text-gray-900">{{ $poll->question }}</div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $poll->is_multiple_choice ? 'Choix multiples' : 'Choix unique' }}
                                        @if($poll->closes_at)
                                            • clôture : {{ $poll->closes_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>

                                    @if(!$poll->isClosed())
                                        <form method="POST" action="{{ route('threads.polls.vote', [$group, $thread]) }}" class="mt-4 space-y-3">
                                            @csrf
                                            <div class="grid gap-2">
                                                @foreach($poll->options as $opt)
                                                    @php($optVotes = (int) $poll->votes->where('poll_option_id', $opt->id)->count())
                                                    @php($pct = $totalVotes > 0 ? (int) round(($optVotes / $totalVotes) * 100) : 0)
                                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
                                                        @if($poll->is_multiple_choice)
                                                            <input type="checkbox" name="option_ids[]" value="{{ $opt->id }}" @checked(in_array((int) $opt->id, $myVoteOptionIds, true)) class="rounded border-gray-300 text-gray-900 focus:ring-gray-400" />
                                                        @else
                                                            <input type="radio" name="option_id" value="{{ $opt->id }}" @checked(in_array((int) $opt->id, $myVoteOptionIds, true)) class="border-gray-300 text-gray-900 focus:ring-gray-400" />
                                                        @endif
                                                        <span class="flex-1 min-w-0">
                                                            <span class="font-semibold text-gray-900">{{ $opt->label }}</span>
                                                            <span class="ms-2 text-xs text-gray-500">({{ $optVotes }})</span>
                                                        </span>
                                                        <span class="text-xs font-semibold text-gray-700">{{ $pct }}%</span>
                                                    </label>
                                                @endforeach
                                            </div>

                                            <div class="flex items-center justify-between">
                                                @error('option_id')
                                                    <div class="text-sm text-red-600">{{ $message }}</div>
                                                @else
                                                    @error('option_ids')
                                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                                    @else
                                                        <div class="text-xs text-gray-500">{{ $totalVotes }} vote{{ $totalVotes > 1 ? 's' : '' }}</div>
                                                    @enderror
                                                @enderror

                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition">
                                                    Voter
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="mt-4 text-sm text-gray-700">Sondage clôturé.</div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-3 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-700 shadow-sm">
                                    Aucun sondage pour le moment.

                                    @if($canModerate)
                                        <form method="POST" action="{{ route('threads.polls.store', [$group, $thread]) }}" class="mt-4 space-y-3">
                                            @csrf

                                            <div>
                                                <label for="question" class="block text-sm font-semibold text-gray-900">Question</label>
                                                <input id="question" name="question" type="text" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                                @error('question')
                                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                        <input type="checkbox" name="is_multiple_choice" value="1" class="rounded border-gray-300 text-gray-900 focus:ring-gray-400" />
                                                        Choix multiples
                                                    </label>
                                                </div>

                                                <div>
                                                    <label for="closes_at" class="block text-sm font-semibold text-gray-900">Clôture (optionnel)</label>
                                                    <input id="closes_at" name="closes_at" type="datetime-local" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" />
                                                    @error('closes_at')
                                                        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="grid gap-3">
                                                <div class="text-sm font-semibold text-gray-900">Options</div>
                                                @for($i = 0; $i < 4; $i++)
                                                    <input name="options[]" type="text" class="block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" placeholder="Option {{ $i + 1 }}" />
                                                @endfor
                                                @error('options')
                                                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="flex items-center justify-end">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition">
                                                    Créer le sondage
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-8">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">Discussion</div>
                                    <div class="mt-1 text-lg font-semibold text-gray-900">Messages</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $posts->count() }} message{{ $posts->count() > 1 ? 's' : '' }}</div>
                            </div>

                            <div class="mt-4 grid gap-3">
                                @forelse($posts as $post)
                                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $post->author?->name ?? 'Membre' }}
                                                </div>
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $post->created_at->diffForHumans() }}
                                                </div>
                                            </div>

                                            @php($canDeletePost = $canModerate || ((int) $post->user_id === (int) auth()->id()))
                                            @if($canDeletePost)
                                                <form method="POST" action="{{ route('threads.posts.destroy', [$group, $thread, $post]) }}" onsubmit="return confirm('Supprimer ce message ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-900 hover:bg-gray-50 transition">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-sm text-gray-800 whitespace-pre-line">{{ $post->body }}</div>

                                        @if(!empty($post->attachments))
                                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                                @foreach($post->attachments as $att)
                                                    @if(($att['type'] ?? null) === 'image' && !empty($att['url']))
                                                        <a href="{{ $att['url'] }}" target="_blank" class="block overflow-hidden rounded-xl border border-gray-200 bg-white">
                                                            <img src="{{ $att['url'] }}" alt="" class="block w-full h-auto" loading="lazy" />
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600 shadow-sm">
                                        Aucun message pour le moment. Sois le premier à répondre.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                            @if($thread->is_locked)
                                <div class="text-sm text-gray-700">
                                    Ce sujet est verrouillé. Tu ne peux plus répondre.
                                </div>
                            @else
                                <form method="POST" action="{{ route('threads.posts.store', [$group, $thread]) }}" class="space-y-3" enctype="multipart/form-data">
                                    @csrf
                                    <div>
                                        <label for="body" class="block text-sm font-semibold text-gray-900">Répondre</label>
                                        <textarea id="body" name="body" rows="4" class="mt-2 block w-full rounded-2xl border-gray-200 bg-white focus:border-gray-400 focus:ring-gray-400" placeholder="Écris ton message…">{{ old('body') }}</textarea>
                                        @error('body')
                                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="images" class="block text-sm font-semibold text-gray-900">Images (optionnel)</label>
                                        <input id="images" name="images[]" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm text-gray-700" />
                                        @error('images')
                                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                        @error('images.*')
                                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-1 text-xs text-gray-500">Jusqu’à 4 images • 4 Mo max par image.</div>
                                    </div>

                                    <div class="flex items-center justify-between gap-3">
                                        @if (session('status') === 'post-created')
                                            <div class="text-sm font-medium text-green-700">Message envoyé.</div>
                                        @else
                                            <div></div>
                                        @endif

                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition">
                                            Envoyer
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <div class="text-sm text-gray-600">Informations</div>
                        <div class="mt-3 grid gap-2 text-sm text-gray-700">
                            <div><span class="text-gray-500">Créé le :</span> {{ $thread->created_at->format('d/m/Y H:i') }}</div>
                            <div><span class="text-gray-500">Dernière activité :</span> {{ optional($thread->last_activity_at)->format('d/m/Y H:i') ?? $thread->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        @if(auth()->user()?->role === 'admin')
                            <a href="/admin/threads/{{ $thread->id }}/edit" class="mt-5 inline-flex w-full items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50 transition">
                                Modifier dans l’admin
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
