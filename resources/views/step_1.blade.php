@extends('layouts.app')

@section('sidebar')
    <div class="sidebar">
        <p>Informatie</p>


        <ul>
            <li>Invoer gegevens</li>
            <li>Aanmaken pseudoniem</li>
        </ul>
    </div>


@endsection

@section('content')
    <div class="content-wrapper">
        {{-- Top info card --}}
        <div class="info-card">
            <h2 class="info-card-title">Huisarts bloedspoed</h2>

            <div class="info-row">
                <span class="info-label">Patient</span>
                <span class="info-value">BSN {{ $patient ?? '' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Zorgcontext</span>
                <span class="info-value">{{ $data_domain ?? 'xxx' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Organisatietype</span>
                <span class="info-value">{{ $organisation_type ?? 'Org 1' }}</span>
            </div>
        </div>

        {{-- Middle section with request flow --}}
        <div class="request-flow">
            <div class="request-card">
                <div class="request-card-corner"></div>
                <div class="request-card-content">
                    <p><strong>Geblindeerd BSN:</strong> {{ $prs_input }}</p>
                    <p><strong>scope:</strong> {{ $scope ?? 'NVI' }}</p>
                    <p><strong>organisatie:</strong> {{ $organisatie ?? 'VWS' }}</p>
                </div>
            </div>

            <div class="flow-arrow">
                <div class="arrow-text">
                    Verstuur Geblindeerd<br>
                    Pseudoniem verzoek<br>
                    naar de PRS
                </div>
                <div class="arrow-line"></div>
            </div>

            <div class="prs-section">
                <span class="prs-label">PRS</span>
                <div class="prs-boxes">
                    <div class="prs-box"></div>
                    <div class="prs-box"></div>
                </div>
            </div>
        </div>

        {{-- Bottom button --}}
        <div class="button-wrapper">
            <form method="POST" action="{{ route('step_2') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Volgende stap</button>
            </form>
        </div>
    </div>
@endsection

@section('explanation')
    <div class="explanation">
        <p>
            At this point, we have a pseudonymized BSN and a scope that indicates which data domain we want to access.<br>
            <br>
            In order to send this information to a third party (like the NVI), we need to pseudonimize the BSN again, so that the
            NVI cannot link the pseudonymized BSN to the original BSN.<br>
            <br>
            This is done by sending the pseudonymized BSN to the PRS, which will return a new pseudonymized BSN that can be used by the NVI.<br>
        </p>
    </div>
@endsection

