<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 42rem)"
    aria-label="{{ __('Main navigation') }}"
    class="collapsible">
    <div class="collapsing-element">
        <ul>
            <li>
                <a href="{{ route('landing.home') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'landing.home') aria-current="page" @endif><span class="icon icon-home">Home-icoon</span>@lang('Landing')</a>
                <a href="{{ route('timeline.home') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'timeline.home') aria-current="page" @endif><span class="icon icon-home">Home-icoon</span>@lang('Timeline')</a>
            </li>
        </ul>
    </div>
</nav>

@if(!request()->routeIs('home'))
<nav class="breadcrumb-bar">
    <div>
        <ul>
            <li><a href="{{ route('index') }}"><span class="icon icon-home">Home-icoon</span>@lang('Landing')</a></li>
        </ul>
    </div>
</nav>
@endif
