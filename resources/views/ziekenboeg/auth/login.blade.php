@extends('layouts.ziekenboeg.app-with-sidebar')

@section('sidemenu')
    <nav class="ziekenboeg-sidemenu">
        <div>
            <div class="sticky-top">
                <h3>Inloggen bij de Ziekenboeg</h3>

                <p>De inlogpagina van De Ziekenboeg is een voorbeeld van hoe zorgplatforms Dezi-Online kunnen aansluiten naast andere inlogmethoden.</p>

                <p>
                    Klik op <b>“Inloggen met Dezi-Online”</b> om verder te gaan.
                </p>
            </div>
            <div class="sticky-bottom">
                <ul>
                    <li>
                        <a href="{{ route('index') }}" class="back-to-proeftuin">
                            Terug naar de Proeftuin hoofdpagina
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endsection

@section('content')
    <section>
        <div class="layout-authentication">
            <h1>@lang('Login')</h1>
            <p>Welkom op de demo-omgeving van het platform van De Ziekenboeg.</p>
            <p>Log in om toegang te krijgen tot de beveiligde omgeving.</p>

            <form method="POST" autocomplete="off">
                <fieldset>
                    <div>
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" disabled>
                    </div>

                    <div>
                        <label for="password">Wachtwoord</label>
                        <input id="password" type="password" name="password" disabled>
                    </div>

                    <p>
                        <a href="#">
                            Wachtwoord vergeten?
                        </a>
                    </p>

                    <button disabled>
                        Log in
                    </button>
                </fieldset>
            </form>

            <p>Of log in met Dezi-Online:</p>
            <ul class="external-login">
                <li><a href="{{ route('oidc.login') }}"><img src="{{ asset('img/login-methods/signin-method-logo.png') }}" alt="">@lang('Login with') Dezi-Online <i class="icon icon-chevron-right icon-small"></i></a></li>
            </ul>
        </div>
    </section>
@endsection
