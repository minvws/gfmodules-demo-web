@extends('layouts.app')

@section('sidebar')
    <div class="sidebar">
        <p>Informatie</p>

        <ul>
            <li>Invoer gegevens</li>
        </ul>

    </div>

@endsection

@section('content')
    <div class="header">
        <div class="header-left">
            <h1>Huisarts bloedspoed</h1>
            <p class="subtitle">De heer Bloedspoed</p>
        </div>
        <div class="header-right">
            EPD
        </div>
    </div>

    <form class="form-section" method="POST" action="{{ route('step_1') }}">
        @csrf
        <div class="form-group">
            <label class="required">Vindt patient</label>
            <input name="bsn" type="text" maxlength="9" placeholder="BSN nummer">
        </div>

        <div class="form-group">
            <label class="required">
                Zorgcontext
{{--                <span class="sub-label">(datadomein)</span>--}}
            </label>
            <select name="datadomain">
                <option value="">Selecteer</option>
                @foreach($datadomains as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>
                Organisatietype
                <span class="sub-label">(optioneel)</span>
            </label>
            <select name="organisation_type">
                <option value="">Selecteer</option>
                @foreach($organisation_types as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="buttons-section">
            <button type="submit" class="btn btn-primary" formaction="{{ route('step_1') }}" formmethod="POST">Demo Lokaliseren</button>
{{--            <button type="submit" class="btn btn-secondary" formaction="{{ route('locate') }}" formmethod="POST">NVI Lokaliseren</button>--}}
        </div>
    </form>

@endsection


@section('explanation')
    <div class="explanation">
        <p>
        Here we are going to enter the BSN of the patient we want to fetch information from.
        We need to specify which type of data domain we want to fetch.
        </p>
        <br/>

        <p>
        Optionally, we can specify the type of organization we are, which can be used for logging and monitoring purposes.
        </p>
    </div>
@endsection
