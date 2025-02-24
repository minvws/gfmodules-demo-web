@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>TIMELINE RESULT</h1>

            @if (count($errors) > 0)
                <div class="error" role="group" aria-label="foutmelding">
                    <h2>Foutmeldingen</h2>
                    <ul>
                        @foreach ($errors as $error)
                            <li>{{ $error['details'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($patient)
            <h2>Tijdslijn van {{ $patient['display'] }} <small>({{$bsn}})</small></h2>
            @endif

            @php
                $currentDate = \Carbon\Carbon::now();
                $pastMedications = [];
                $currentMedications = [];
                $futureMedications = [];

                foreach ($medicationStatements as $entry) {
                    $startDate = \Carbon\Carbon::parse($entry['resource']['dateAsserted'] ?? null);
                    $endDate = \Carbon\Carbon::parse($entry['resource']['dateAsserted'] ?? null);

                    if ($endDate->isBefore($currentDate)) {
                        $pastMedications[] = $entry;
                    } elseif ($startDate->isAfter($currentDate)) {
                        $futureMedications[] = $entry;
                    } else {
                        $currentMedications[] = $entry;
                    }
                }

                $medicationCategories = [
                    'Huidige Medicatie' => $currentMedications,
                    'Verleden Medicatie' => $pastMedications,
                    'Toekomstige Medicatie' => $futureMedications
                ];
            @endphp
            <!-- Filter: alle medicatie die in de afgelopen 2 maanden geëindigd of gestopt is. -->
            <!-- Filter: alle medicatie die in de komende 3 maanden actueel wordt. Dit is inclusief voorgenomen medicatiegebruik. -->

            <!--
            Type Medicatieafspraak of Toedieningsafspraak of Medicatiegebruik
            Geneesmiddel Medicatieafspraak:AfgesprokenGeneesmiddel of Toedieningsafspraak:GeneesmiddelBijToedieningsafspraak of Medicatiegebruik:Gebruiksproduct -> Altijd: Product – ProductCode (als afwezig: ProductSpecificatie, ProductNaam)
            Ingangsdatum startDatumTijd
            Stopdatum/Duur eindDatumTijd(als afwezig: tijdsDuur)
            Dosering Gebruiksinstructie – Omschrijving
            Toedieningsweg Gebruiksinstructie – Toedieningsweg
            Reden Medicatieafspraak:RedenVanVoorschrijven & evt. RedenWijzigenOfStaken of Toedieningsafspraak:ToedieningsafspraakRedenWijzigenOfStaken of Medicatiegebruik:RedenWijzigenOfStoppenGebruik
            Toelichting Toelichting & (Toedieningsafspraak)AanvullendeInformatie of Medicatiegebruik:Toelichting
            Bron Medicatieafspraak:Voorschrijver of Toedieningsafspraak:Verstrekker of Medicatiegebruik:Auteur of "Patient" -> Altijd: Zorgverlener – Naamgegevens, Specialisme
            Actie</th>
            Ura</th> -->

            @foreach ($medicationCategories as $category => $medications)
                <h2>{{ $category }}</h2>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>Geneesmiddel</th>
                        <th>Ingangsdatum</th>
                        <!--<th>Stopdatum/Duur</th>-->
                        <th>Dosering</th>
                        <th>Toedieningsweg</th>
                        <th>Reden</th>
                        <th>Toelichting</th>
                        <th>Bron</th>
                        <th>Actie</th>
                        <th>Ura</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($medications as $entry)
                        <tr>
                            <td>{{ ['Medicatieafspraak', 'Toedieningsafspraak', 'Medicatiegebruik'][array_rand(['Medicatiegebruik'])] }}</td> <!-- type -->
                            <!--<td>{{ $entry['resource']['medicationCodeableConcept']['coding'][0]['display'] ?? '-'}}</td>  Geneesmiddel -->
                            <td>{{ $entry['resource']['medicationCodeableConcept']['coding'][0]['display'] ?? entry['resource']['medicationReference']['display'] ?? '-' }}</td>  <!-- Geneesmiddel -->
                            <td>{{ \Carbon\Carbon::parse($entry['resource']['dateAsserted'] ?? null)->format('d M Y') }}</td> <!-- Ingangsdatum -->
                            <!--<td>{{ \Carbon\Carbon::parse($entry['resource']['effectivePeriod']['end'] ?? null)->format('d M Y') }}</td> <!-- Stopdatum/Duur -->
                            <td>{{ $entry['resource']['dosage'][0]['doseAndRate'][0]['type']['coding'][0]['display'] ?? '-' }} {{ number_format($entry['resource']['dosage'][0]['doseAndRate'][0]['doseQuantity']['value'] ?? 0, 2) }} {{ $entry['resource']['dosage'][0]['doseAndRate'][0]['doseQuantity']['unit'] ?? '-' }}</td> <!-- Dosering -->
                            <td>{{ $entry['resource']['dosage'][0]['route']['coding'][0]['display'] ?? '-' }}</td> <!-- Toedieningsweg -->
                            <td>{{ $entry['resource']['reasonCode'][0]['coding'][0]['display'] ?? '-' }}</td> <!-- Reden -->
                            <td>{{ $entry['resource']['note'][0]['text'] ?? '-' }}</td> <!-- Toelichting -->
                            <td>{{ $entry['resource']['informationSource']['display'] ?? '-' }}</td> <!-- Bron -->
                            <td>{{ $entry['resource']['id'] }}</td>
                            <td><a href="{{route('timeline.org_info', ['ref' => $entry['references']['addressingInformation']['organizationId'] ])}}">{{ $entry['references']['addressingInformation']['ura'] }}</a></td>
                            <!--<td>{{ json_encode($entry['resource']) }}</td>-->
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach

        </div>
    </section>
@endsection
