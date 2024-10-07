@props([
    'logoComponent' => 'header-logo-default',
    'navigationComponent' => 'header-navigation-bar-default',
])

<header class="page-header">
    <x-header-skip-to-content />
    <div>
        <x-dynamic-component :component="$logoComponent" />
        <x-dynamic-component :component="$navigationComponent" />
    </div>
</header>
