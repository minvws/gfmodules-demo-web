@extends('layouts.guest')

@section('content')
    @if (session()->has('error'))
        <section role="alert" class="error no-print" aria-label="{{ __('error') }}">
            <div>
                <h4>{{ session('error') }}</h4>
                <p>{{ session('error_description') }}</p>
            </div>
        </section>
    @endif

    <section class="layout-form">
        <div>
            <h1>Informatie opvragen</h1>

            <ul class="accordion">
                <li>
                    <button aria-expanded="{{ !$state->getConsentData() ? "true" : "false" }}" id="flow-identification-authentication">1. Identificatie en Authenticatie</button>
                    <div aria-labelledby="flow-identification-authentication">
                        @if(!$state->getUser())
                        <ul class="external-login">
                            <li>
                                <a href="{{ route('oidc.login') }}">
                                    <img src="{{ asset('img/signin-method-logo.png') }}" alt="" rel="external">
                                    @lang('Login with') Dezi-online
                                </a>
                            </li>
                        </ul>
                        @else
                            <p>Je bent ingelogd als: {{ $state->getUser()->getName() }}</p>
                        @endif
                    </div>
                </li>
                <li>
                    <button aria-expanded="{{ $state->getUser() ? "true" : "false" }}" id="flow-consent">2. Toestemming</button>
                    <div aria-labelledby="flow-consent">

                        Hier moet ook een formulier komen...

                    </div>
                </li>
                <li>
                    <button aria-expanded="false" id="flow-authorization">3. Autorisatie</button>
                    <div aria-labelledby="flow-authorization">

                        <!-- Voeg hier de content toe -->

                    </div>
                </li>
            </ul>
{{--            TODO: Add CSRF and POST to flow --}}
            <form class="inline">
                <button type="submit">Informatie opvragen</button>
            </form>

        </div>
    </section>
@endsection
