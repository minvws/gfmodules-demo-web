<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 42rem)"
    aria-label="{{ __('Main navigation') }}"
    class="collapsible">
    <div class="collapsing-element">
        <ul>
            <li>
                <a href="{{ route('index') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'index') aria-current="page" @endif><span class="icon icon-home">Home-icoon</span>@lang('Labs')</a>
            </li>
            <li>
                <a href="{{ route('about') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'about') aria-current="page" @endif>@lang('About') de proeftuin</a>
            </li>
        </ul>
    </div>
</nav>

@if(!request()->routeIs('index'))
<nav class="breadcrumb-bar">
    <div>
        <ul>
            <li><a href="{{ route('index') }}"><span class="icon icon-home">Home-icoon</span>@lang('Labs')</a></li>
            @if(request()->routeIs('about'))
                <li><a href="{{ route('about') }}" aria-current="page">@lang('About') de proeftuin</a></li>
            @endif
            @if(request()->routeIs('test-identities'))
            <li><a href="{{ route('test-identities') }}" aria-current="page">Testidentiteiten en inlogmiddelen</a></li>
            @endif
        </ul>
    </div>
</nav>
@endif
