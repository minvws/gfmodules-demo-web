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
            <h1>@lang('About') de proeftuin</h1>
            <p class="emphasized">De proeftuin is een testomgeving voor applicatiebeheerders in de zorg en leveranciers van inlogmiddelen.</p>

            <p>In de proeftuin kan het nieuwe inlogstelsel voor zorgprofesstionals worden getest. Platform- en Middelenleveranciers kunnen ervaren hoe inloggen via Dezi-Online werkt en kunnen zelf aan de slag om een aansluiting op Dezi-Online te maken.</p>
            <p>De proeftuin is ontwikkeld door het Ministerie van Volksgezondheid, Welzijn en Sport. Het nieuwe stelsel is nog in ontwikkeling. Daarmee verandert ook de proeftuin de komende tijd. Op die manier testen we samen wat het beste werkt. Ondersteuning en onderhoud van de proeftuin geschiedt op best effort tijdens kantooruren.</p>
        </div>
    </section>

    <section>
        <div>
            <h2>Versiegeschiedenis</h2>
            <p>De laatste update van de proeftuin was op 29-04-2024.</p>
            <table class="version-history">
                <caption>Versieoverzicht:</caption>
                <thead>
                <tr>
                    <th scope="col" class="version-history-version-number">Versienummer</th>
                    <th scope="col" class="version-history-date">Toegepast op</th>
                    <th scope="col" class="version-history-changes">Wijzigingen</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>v1.3.0</td>
                    <td>29-04-2024</td>
                    <td>Naamswijziging van UZI naar Dezi.</td>
                </tr>
                <tr>
                    <td>v1.2.0</td>
                    <td>29-02-2024</td>
                    <td>Documentatie van "Aansluiten als middelenleverancier" bijgewerkt.</td>
                </tr>
                <tr>
                    <td>v1.1.0</td>
                    <td>31-01-2024</td>
                    <td>Tekst aanpassingen op de testidentiteiten pagina en een "We kunnen u niet inloggen" pagina toegevoegd aan de ziekenboeg.</td>
                </tr>
                <tr>
                    <td>v1.0.2</td>
                    <td>26-01-2024</td>
                    <td>Verwijzing van "Aansluiten als middelenleverancier" documentatie toegevoegd.</td>
                </tr>
                <tr>
                    <td>v1.0.1</td>
                    <td>24-01-2024</td>
                    <td>Verwijzing van "Aansluiten als platformleverancier" documentatie toegevoegd.</td>
                </tr>
                <tr>
                    <td>v1.0.0</td>
                    <td>04-12-2023</td>
                    <td>Eerste release proeftuin.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="background-color-offset">
        <div>
            <h2>Privacy</h2>
            <p>
                In de <a href="https://www.rijksoverheid.nl/ministeries/ministerie-van-volksgezondheid-welzijn-en-sport/privacy">privacyverklaring van het Ministerie van Volksgezondheid, Welzijn en Sport</a> is te lezen hoe VWS omgaat met gegevensbescherming.
            </p>
            <p>
                In de proeftuin wordt gewerkt met testdata. Wij registreren alleen het IP-adres van het gebruikte apparaat. Dit is noodzakelijk voor de communicatie met de webservers van het Ministerie van Volksgezondheid, Welzijn en Sport. Het IP-adres wordt niet langer bewaard dan nodig is met oog op beveiliging, beheer en het opsporen van fouten in de proeftuin. Verder worden er geen persoonsgegevens verwerkt in de proeftuin.
            </p>
        </div>
    </section>

    <section>
        <div>
            <h2>Contact</h2>
            <p>Vragen over de proeftuin of het nieuwe inlogstelsel Dezi kunnen worden gesteld via <a href="mailto:info@dezi.nl">info@dezi.nl</a>.</p>
        </div>
    </section>
@endsection
