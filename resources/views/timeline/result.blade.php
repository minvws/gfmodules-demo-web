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
                            <li>{{ $error['details'] }}
                                @if (isset($error['diagnostics']) && config('app.show_diagnostics'))
                                    <pre>{{ $error['diagnostics'] }}</pre>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($patient)
                <h2>Tijdslijn van {{ $patientName }} <small>({{$bsn}})</small></h2>
            @endif

            @if (!empty($requested_resources))
                @php
                    $resourceType = $requested_resources[0]['resourceType'] ?? 'Unknown';
                @endphp

                @if ($resourceType === 'ImagingStudy')
                    @include('timeline.partials.imaging-study', ['requested_resources' => $requested_resources])
                @elseif ($resourceType === 'MedicationStatement')
                    @include('timeline.partials.medication-statement', ['requested_resources' => $requested_resources])
                @endif
            @else
                <p>Geen gegevens gevonden.</p>
            @endif
        </div>
    </section>
@endsection
