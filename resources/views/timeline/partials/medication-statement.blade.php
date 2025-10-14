{{-- MedicationStatement Table --}}
@php
    $currentDate = \Carbon\Carbon::now();
    $categories = [
        'Huidige Medicatie' => [],
        'Verleden Medicatie' => [],
        'Toekomstige Medicatie' => []
    ];

    foreach ($requested_resources as $resource) {
        $startDate = $resource['effectivePeriod']['start'] ?? $resource['effectiveDateTime'] ?? $resource['dateAsserted'] ?? null;
        $endDate = $resource['effectivePeriod']['end'] ?? null;

        if ($endDate && \Carbon\Carbon::parse($endDate)->isBefore($currentDate)) {
            $categories['Verleden Medicatie'][] = $resource;
        } elseif ($startDate && \Carbon\Carbon::parse($startDate)->isAfter($currentDate)) {
            $categories['Toekomstige Medicatie'][] = $resource;
        } else {
            $categories['Huidige Medicatie'][] = $resource;
        }
    }
@endphp

@foreach ($categories as $categoryName => $medications)
    @if (!empty($medications))
        <h2>{{ $categoryName }}</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
            <tr>
                <th>Type</th>
                <th>Geneesmiddel</th>
                <th>Ingangsdatum</th>
                <th>Stopdatum</th>
                <th>Dosering</th>
                <th>Toedieningsweg</th>
                <th>Reden</th>
                <th>Toelichting</th>
                <th>Informatie bron</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($medications as $medication)
                <tr>
                    <td>Medicatiegebruik</td>
                    <td>{{ $medication['medicationCodeableConcept']['coding'][0]['display'] ?? $medication['medicationReference']['display'] ?? '-' }}</td>
                    <td>{{ isset($medication['effectivePeriod']['start']) || isset($medication['effectiveDateTime']) || isset($medication['dateAsserted']) ? \Carbon\Carbon::parse($medication['effectivePeriod']['start'] ?? $medication['effectiveDateTime'] ?? $medication['dateAsserted'])->format('d M Y') : '-' }}</td>
                    <td>{{ isset($medication['effectivePeriod']['end']) ? \Carbon\Carbon::parse($medication['effectivePeriod']['end'])->format('d M Y') : '--' }}</td>
                    <td>
                        @if (isset($medication['dosage'][0]['doseAndRate'][0]))
                            {{ $medication['dosage'][0]['doseAndRate'][0]['type']['coding'][0]['display'] ?? '-' }}
                            {{ isset($medication['dosage'][0]['doseAndRate'][0]['doseQuantity']['value']) ? number_format($medication['dosage'][0]['doseAndRate'][0]['doseQuantity']['value'], 2) : '' }}
                            {{ $medication['dosage'][0]['doseAndRate'][0]['doseQuantity']['unit'] ?? '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $medication['dosage'][0]['route']['coding'][0]['display'] ?? '-' }}</td>
                    <td>{{ $medication['reasonCode'][0]['coding'][0]['display'] ?? '-' }}</td>
                    <td>{{ $medication['note'][0]['text'] ?? '-' }}</td>
                    <td>{{ $medication['informationSource']['display'] ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endforeach
