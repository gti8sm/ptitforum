@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-900 text-sm font-semibold border border-gray-200 transition'
            : 'block w-full px-4 py-2 rounded-lg text-gray-700 text-sm font-semibold hover:bg-gray-50 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
