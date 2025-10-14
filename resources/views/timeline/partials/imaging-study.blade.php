{{-- ImagingStudy Table --}}
<table class="table table-bordered table-striped" id="timeline-result-table">
    <thead class="table-dark">
    <tr>
        <th>Datum</th>
        <th>Tijd</th>
        <th>Modaliteit</th>
        <th>Omschrijving</th>
        <th>Beelden</th>
        <th>Lichaamsdeel</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($requested_resources as $resource)
        @foreach ($resource['series'] ?? [] as $series)
            <tr>
                <td>{{ isset($resource['started']) ? \Carbon\Carbon::parse($resource['started'])->format('d M Y') : '-' }}</td>
                <td>{{ isset($resource['started']) ? \Carbon\Carbon::parse($resource['started'])->format('H:i') : '-' }}</td>
                <td>{{ $series['modality']['display'] ?? '-' }}</td>
                <td>{{ $series['description'] ?? $resource['description'] ?? '-' }}</td>
                <td>{{ count($series['instance'] ?? []) }}</td>
                <td>{{ $series['bodySite']['display'] ?? '-' }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
