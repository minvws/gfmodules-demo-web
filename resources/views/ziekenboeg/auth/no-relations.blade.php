@extends('layouts.ziekenboeg.app-with-sidebar')

@php
/* @var $user \App\Models\UziUser */
@endphp

@section('sidemenu')
    <nav class="ziekenboeg-sidemenu">
        <div>
            <div class="sticky-top">
                <h3>Identiteit heeft geen toegang tot de Ziekenboeg</h3>

                <p>
                    Enkele (test)identiteiten hebben geen toegang tot de Ziekenboeg; de identiteit in het proeftuin UZI-register bevat geen URA-relatie met de Ziekenboeg. Zo kunnen ook incorrecte inlogpogingen getest worden.
                </p>

                <p>
                    Correcte inlogpogingen kunnen worden getest met een andere (test)identiteit.
                </p>

                <p>
                    Klik op <b>“Terug naar inloggen”</b> om verder te gaan.
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
        <div>
            <h1>We kunnen u niet inloggen bij de Ziekenboeg.</h1>
            <p>Het inloggen in de Ziekenboeg is niet gelukt omdat er geen ziekenboegrelatie aanwezig is.</p>
            <p>We hebben de volgende gegevens voor deze testidentiteit uit het UZI-register opgehaald:</p>
        </div>
    </section>

    <section>
        <div>
            <h2>Gegevens</h2>
            <dl>
                <div>
                    <dt>Initialen</dt>
                    <dd>{{ $user->initials }}</dd>
                </div>
                <div>
                    <dt>Tussenvoegsel</dt>
                    <dd>{{ $user->surnamePrefix }}</dd>
                </div>
                <div>
                    <dt>Achternaam</dt>
                    <dd>{{ $user->surname }}</dd>
                </div>
                <div>
                    <dt>UZI-nummer</dt>
                    <dd>{{ $user->uziId }}</dd>
                </div>
                <div>
                    <dt>Level of Assurance</dt>
                    <dd>{{ $user->loaAuthn }}</dd>
                </div>
            </dl>

            <a href="{{ route('ziekenboeg.login') }}" class="button">Terug naar inloggen</a>
        </div>
    </section>
@endsection
