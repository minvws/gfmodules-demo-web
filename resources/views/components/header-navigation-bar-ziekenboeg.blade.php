<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 42rem)"
    aria-label="{{ __('Main navigation') }}"
    class="collapsible">
    <div class="collapsing-element">
        <ul>
            @isZiekenboegUsersPage
            @auth
                <li>
                    <a href="{{ route('ziekenboeg.users.home') }}"
                       @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'ziekenboeg.users.home') aria-current="page" @endif>Home</a>
                </li>
                <li>
                    <a href="#">Patiënten</a>
                </li>
                <li>
                    <a href="#">Praktijkinformatie</a>
                </li>
                <li>
                    <a href="{{ route('ziekenboeg.contact') }}"
                       @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'ziekenboeg.contact') aria-current="page" @endif>Contact</a>
                </li>
            @endauth
            @endisZiekenboegUsersPage

            @notZiekenboegUsersPage
            <li>
                <a href="{{ route('ziekenboeg.home') }}"
                   @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'ziekenboeg.home') aria-current="page" @endif>Home</a>
            </li>
            <li>
                <a href="#">Praktijkinformatie</a>
            </li>
            <li>
                <a href="#">Onze artsen</a>
            </li>
            <li>
                <a href="#">Nieuws</a>
            </li>
            <li>
                <a href="{{ route('ziekenboeg.contact') }}"
                   @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'ziekenboeg.contact') aria-current="page" @endif>Contact</a>
            </li>
            @endnotZiekenboegUsersPage
        </ul>

        <ul>
            @notZiekenboegUsersPage
            @if(\Illuminate\Support\Facades\Route::currentRouteName() !== 'ziekenboeg.login')
                <li>
                    <a class="button" href="{{ route('ziekenboeg.login') }}"><span
                            class="icon icon-user">@lang('User icon')</span> Inloggen medewerkers</a>
                </li>
            @endif
            @endnotZiekenboegUsersPage

            @isZiekenboegUsersPage
            @auth
                <li>
                    <div>Ingelogd als: {{ auth()->user()->getDisplayName() }} | <x-header-logout-button/></div>
                </li>
            @endauth
            @endisZiekenboegUsersPage
        </ul>
    </div>
</nav>
