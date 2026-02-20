@extends('layouts.app')

@section('sidebar')
    <div class="sidebar">
        <p>Informatie</p>

        <ul>
            <li>Invoer gegevens</li>
            <li>Aanmaken pseudoniem</li>
            <li>Verzending naar de PRS</li>
            <li>Verzending naar de NVI</li>
            <li>Antwoord van NVI</li>
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


        <h2>Lijst met gevonden zorgverleners</h2>
        <table>
            <thead>
                <tr>
                    <td>Resource Type</td>
                    <td>URA</td>
                    <td>Type</td>
                </tr>
            </thead>
            <tbody>
                @foreach($organizations as $org)
                    <tr>
                        <td>{{ $org['resource']['resourceType'] }}</td>
                        <td>{{ $org['resource']['id'] }}</td>
                        <td>{{ $org['resource']['type'][0]['coding'][0]['display'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Bottom button --}}
        <div class="button-wrapper">
            <form method="POST" action="{{ route('locate') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Volgende stap</button>
            </form>
        </div>
    </div>
@endsection

@section('explanation')
    <div class="explanation">
        <p>
            We have received an answer from the NVI which containts zero or more organizations that holds information for our patient.<br>
            <br>
            At no point in the process did we share the patient's BSN number with any of the involved parties or are they able to infer
            the BSN number from the data they received.
        </p>
    </div>
@endsection
