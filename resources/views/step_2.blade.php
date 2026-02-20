@extends('layouts.app')

@section('sidebar')
    <div class="sidebar">
        <p>Informatie</p>

        <ul>
            <li>Invoer gegevens</li>
            <li>Aanmaken pseudoniem</li>
            <li>Verzending naar de PRS</li>
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

        {{-- PRS Response Flow --}}
        <div class="response-flow">
            <div class="prs-section">
                <span class="prs-label">PRS</span>
                <div class="prs-boxes">
                    <div class="prs-box"></div>
                    <div class="prs-box"></div>
                </div>
            </div>

            <div class="flow-arrow">
                <div class="arrow-text">
                    De geblindeerde BSN is<br>
                    geëvalueerd door de PRS
                </div>
                <div class="arrow-line"></div>
            </div>

            <div class="jwe-envelope">
                <div class="envelope-content">
                    <p><strong>ENCRYPTED DATA FOR NVI</strong></p>
                </div>
                <div class="envelope-lock">
                    <div class="lock-icon">
                        <div class="lock-body">JWE</div>
{{--                        <div class="lock-shackle"></div>--}}
                    </div>
                </div>
                <div class="envelope-flap"></div>
            </div>
        </div>

        {{-- Bottom button --}}
        <div class="button-wrapper">
            <form method="POST" action="{{ route('step_3') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Volgende stap</button>
            </form>
        </div>
    </div>
@endsection

@section('explanation')
    <div class="explanation">
        <p>
            We have send our pseudonimized BSN to the PRS and we have received a response. This response consists of an encrypted package that can only be
            read by the NVI service.
            <br>
            In no way we can read the content of this package.<br>
        </p>
    </div>
@endsection
