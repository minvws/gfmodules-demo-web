@extends('layouts.ziekenboeg.app-with-sidebar')

@php
/* @var $user \App\Models\UziUser */
@endphp

@section('sidemenu')
    <nav class="ziekenboeg-sidemenu">
        <div>
            <div class="sticky-top">
                <h3>Welkom bij de Ziekenboeg</h3>

                <p>
                    De ingelogde omgeving van de Ziekenboeg.
                </p>

                <p>
                    De demo applicatie geeft aan welke gegevens uit het proeftuin UZI-register zijn opgehaald voor de testidentiteit waarmee is ingelogd.
                </p>

                <p>
                    Op basis van die gegevens kunnen zorgplatforms identiteiten autoriseren.
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
            <h1>U bent succesvol ingelogd bij de Ziekenboeg.</h1>
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

            <h3>@lang('Relations')</h3>
            @foreach($user->uras as $key => $ura)
                <b>Relatie {{ $key + 1 }}</b>
                <dl>
                    <div>
                        <dt>Organisatie</dt>
                        <dd>{{ $ura->entityName }}</dd>
                    </div>
                    <div>
                        <dt>URA</dt>
                        <dd>{{ $ura->ura }}</dd>
                    </div>
                    <div>
                        <dt>Rolcodes</dt>
                        <dd>@foreach($ura->roles as $role)
                                {{ $role->code }} ({{$role->name}}) <br>
                            @endforeach</dd>
                    </div>
                </dl>
            @endforeach
        </div>
    </section>
@endsection
