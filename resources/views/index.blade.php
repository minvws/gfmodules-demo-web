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

    <section>
        <div>
            <h1>Welkom bij de proeftuin</h1>
            <p class="emphasized">De proeftuin is een omgeving voor platform- en middelenleveranciers om aansluiting op Dezi-Online te testen.</p>

            <p>Door het nieuwe Dezi-stelsel wordt het voor zorgmedewerkers mogelijk om met goedgekeurde inlogmiddelen in te loggen en medische gegevens te raadplegen en uit te wisselen. Met deze middelen kunnen identificerende kenmerken uit het UZI-register opgehaald worden.</p>
            <p>Op dit moment is het nieuwe stelsel (werknaam: Dezi-Online) in ontwikkeling. Geïnteresseerde partijen kunnen het inloggen via Dezi-Online op de proeftuin uitproberen en op 2 manieren aansluiten:</p>
            <p><a href="#route_1">Route 1: Aansluiten als platformleverancier</a><br><a href="#route_2">Route 2: Aansluiten als middelenleverancier (in ontwikkeling)</a></p>

            <p>Wilt u meer weten over het nieuwe inlogstelsel Dezi? Kijk dan op <a href="https://www.dezi.nl/">dezi.nl</a>.
            </p>
        </div>
    </section>

    <section>
        <div>
            <h2>Zo werkt de proeftuin</h2>
            <p>Om het inloggen met de huidige versie van Dezi-Online en de aansluiting van applicaties en middelen te testen werkt de proeftuin met een eigen versie van het UZI-register. In het UZI-register van de proeftuin zijn <a href="{{ route('test-identities') }}">test zorgidentiteiten</a> opgenomen.</p>
            <p>Het nieuwe stelsel is nog in ontwikkeling. Daarmee verandert ook de proeftuin de komende tijd. Op die manier testen we samen wat het beste werkt.</p>
            <a href="{{ route('about') }}" class="button ghost">Lees meer over de proeftuin</a>
        </div>
    </section>

    <section class="background-color-offset">
        <div>
            <h2>Inloggen via Dezi-Online uitproberen</h2>
            <p>
                In de proeftuin kunt u ervaren hoe inloggen via Dezi-Online gaat werken. We hebben daarom als voorbeeld ‘De Ziekenboeg’ gemaakt. Inloggen bij de Ziekenboeg kan met <a href="{{ route('test-identities') }}">een testidentiteit of de inlogmiddelen die beschikbaar zijn in de proeftuin</a>.
            </p>
            <p>
                Op basis van de identiteit waarmee wordt ingelogd wordt de data die hoort bij de identiteit uit het proeftuin UZI-register opgehaald en na inloggen getoond in de interface van de Ziekenboeg.
            </p>
            <a href="{{ route('ziekenboeg.home') }}" class="button ghost">Inloggen bij de Ziekenboeg</a>
        </div>
    </section>

    <section>
        <div>
            <h2>Aansluiten op de proeftuin</h2>
            <h3 id="route_1">Route 1: Aansluiten als platformleverancier</h3>
            <p>
                De proeftuin maakt het voor platformleveranciers mogelijk om het inloggen via Dezi-Online te testen vanuit de applicaties die zij ontwikkelen. Dezi-Online is gebouwd volgens de OpenID Connect (OIDC) standaard, waardoor aansluiten relatief eenvoudig is. Technische informatie is te vinden in de <a href="https://www.dezi.nl/aansluiten/documenten/publicaties/2024/05/08/koppelvlakspecificatie-dezi-online-koppelvlak-1_-platformleverancier">'Koppelvlak specificatie voor platformleveranciers' (PDF, nl)</a>.
            </p>
            <p>
                Voor het koppelen van een zorgplatform met Dezi-Online, raadpleeg de <a href="https://www.dezi.nl/aansluiten/documenten/publicaties/2024/05/08/aansluitdocument-proeftuin---platformleverancier">'Aansluitdocumentatie proeftuin voor platformleveranciers' (PDF, nl)</a>.
            </p>
            <h3 id="route_2">Route 2: Aansluiten als middelenleverancier (in ontwikkeling)</h3>
            <p>
                De proeftuin maakt het voor leveranciers van (zorgspecifieke) inlogmiddelen mogelijk om aangesloten te worden op Dezi-Online. Deze aansluiting kan vervolgens in ‘De Ziekenboeg’ worden getest. Technische informatie is te vinden in de <a href="https://www.dezi.nl/aansluiten/documenten/publicaties/2024/05/08/koppelvlakspecificatie-dezi-online-koppelvlak-2_-middelenleverancier">'Koppelvlakspecificatie voor middelenleveranciers' (PDF, nl)</a>.
            </p>
            <p>
                Voor het koppelen van een inlogmiddel aan Dezi-Online, raadpleeg de <a href="https://www.dezi.nl/aansluiten/documenten/publicaties/2024/05/08/aansluitdocument-proeftuin---middelenleverancier">'Aansluitdocumentatie proeftuin middelenleveranciers' (PDF, nl)</a>.
            </p>
        </div>
    </section>
@endsection
