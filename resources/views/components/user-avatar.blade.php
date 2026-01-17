@props(['user'])

<x-avatar
    :image="$user->getAvatarUrl()"
    :placeholder="$user->getInitials()"
    {{ $attributes }}
/>
