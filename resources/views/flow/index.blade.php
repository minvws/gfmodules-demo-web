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
                    <button aria-expanded="{{ !$state->getConsentData() ? "true" : "false" }}"
                            id="flow-identification-authentication">1. Identificatie en Authenticatie
                    </button>
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
                    <button aria-expanded="{{ $state->getUser() ? "true" : "false" }}" id="flow-consent">2.
                        Patient
                    </button>
                    <div aria-labelledby="flow-consent">
                        @if(!$state->getConsentData() || $editConsent)
                            <form action="{{ route('flow-consent.store') }}" method="POST">
                                @csrf
                                <fieldset {{ !$state->getUser() ? "disabled" : "" }}>
                                    <div>
                                        <label for="flow-consent-bsn">Burgerservicenummer</label>
                                        <span
                                            class="nota-bene">BSN van de persoon waarvan u de gegevens op wilt vragen</span>
                                        <div>
                                            @error('bsn')
                                            <p class="error" id="flow-consent-bsn-error-message">
                                                <span>Foutmelding:</span> {{ $message }}
                                            </p>
                                            @enderror
                                            <input
                                                id="flow-consent-bsn"
                                                name="bsn"
                                                type="text"
                                                minlength="8"
                                                maxlength="9"
                                                required
                                                aria-describedby="flow-consent-bsn-error-message"
                                                value="{{ old('bsn', $state->getConsentData()?->getBsn()) }}"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <fieldset>
                                            <legend>Toegangstype</legend>
                                            <div>
                                                <div>
                                                    @error('access_type')
                                                    <p class="error" id="flow-consent-access-type-error-message">
                                                        <span>Foutmelding:</span> {{ $message }}
                                                    </p>
                                                    @enderror
                                                    <div class="radio">
                                                        <input type="radio" id="flow-consent-consent" name="access_type" value="treatment_relation"
                                                               class="warning" required
                                                               aria-describedby="flow-consent-access-type-warning-message flow-consent-access-type-error-message flow-consent-consent-warning-message"
                                                               {{ old('access_type', $state->getConsentData()?->getConsentType()?->value) === 'treatment_relation' ? 'checked' : '' }}>
                                                        <label for="flow-consent-consent">Ik heb een behandelrelatie met
                                                            deze patient</label>
                                                        <p class="warning" id="flow-consent-consent-warning-message">
                                                            <span>Waarschuwing:</span> De behandelrelatie wordt steeksproefsgewijs
                                                            gecontroleerd. Indien er geen sprake blijkt te zijn van een geldige
                                                            relatie kan dit tot royement leiden.
                                                        </p>
                                                    </div>
                                                    <div class="radio">
                                                        <input type="radio" id="flow-consent-breaking-glass" name="access_type" value="breaking_glass"
                                                               class="warning"
                                                               aria-describedby="flow-consent-access-type-warning-message flow-consent-access-type-error-message flow-consent-breaking-glass-warning-message"
                                                               {{ old('access_type', $state->getConsentData()?->getConsentType()?->value) === 'breaking_glass' ? 'checked' : '' }}>
                                                        <label for="flow-consent-breaking-glass">Ik verzoek om noodtoegang
                                                            (breaking the glass) tot deze patiëntgegevens</label>
                                                        <p class="warning" id="flow-consent-breaking-glass-warning-message">
                                                            <span>Waarschuwing:</span> Breaking the glass toegang is alleen toegestaan
                                                            in noodsituaties wanneer directe toegang tot patiëntgegevens noodzakelijk is
                                                            voor de zorgverlening. Deze toegang wordt gelogd en gecontroleerd.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <button type="submit">Volgende</button>
                                </fieldset>
                            </form>
                        @else
                            <p>U gaat gegevens opvragen van bsn: {{ $state->getConsentData()?->getBsn() }}</p>
                            <a href="{{ route('flow-consent') }}" class="button ghost">Gegevens wijzigen</a>
                        @endif
                    </div>
                </li>
                <li>
                    <button aria-expanded="{{ $state->getConsentData() && !$editConsent  ? "true" : "false" }}"
                            id="flow-authorization">3. Informatie type
                    </button>
                    <div aria-labelledby="flow-authorization">
                        @if(!$state->getAuthorizationData() || $editAuthorization)
                            <form action="{{ route('flow-authorization.store') }}" method="POST">
                                @csrf
                                <fieldset {{ !$state->getUser() || !$state->getConsentData() ? "disabled" : "" }}>
                                    <div>
                                        <label for="flow-authorization-information-types">Type informatie</label>
                                        <span class="nota-bene">Welke informatietypen wilt u opvragen</span>
                                        <div>
                                            @error('information_types')
                                            <p class="error" id="flow-authorization-information-types-error-message">
                                                <span>Foutmelding:</span> {{ $message }}
                                            </p>
                                            @enderror
                                            @foreach($informationTypes as $informationTypeKey => $informationType)
                                                <div class="checkbox">
                                                    <input type="checkbox"
                                                        id="flow-authorization-information-types-{{ $informationTypeKey }}"
                                                        name="information_types[]" value="{{ $informationTypeKey }}"
                                                        aria-describedby="flow-authorization-information-types-error-message" {{ in_array($informationTypeKey, old('information_types', \App\Enums\DataDomain::toStringArray($state->getAuthorizationData()?->getInformationTypes() ?? []))) ? 'checked' : '' }}>
                                                    <label
                                                        for="flow-authorization-information-types-{{ $informationTypeKey }}">{{ $informationType }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="submit">Volgende</button>
                                </fieldset>
                            </form>
                        @else
                            @php
                                $selectedInformationTypes = collect($informationTypes)->only(\App\Enums\DataDomain::toStringArray($state->getAuthorizationData()->getInformationTypes()))->toArray();
                            @endphp

                            <p>Selectie: {{ implode(', ', $selectedInformationTypes) }}</p>
                            <a href="{{ route('flow-authorization') }}" class="button ghost">Selectie wijzigen</a>
                        @endif
                    </div>
                </li>
            </ul>
            <form class="inline" action="{{ route('flow.retrieve-timeline') }}" method="POST">
                @csrf
                <button
                    type="submit" {{ $state->getConsentData() && $state->getAuthorizationData() ? "" : "disabled" }}>
                    Lokaliseren
                </button>
            </form>
        </div>
    </section>
@endsection
