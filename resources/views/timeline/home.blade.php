@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>TIMELINE</h1>
            <sub>{{ $user->initials }} {{ $user->surnamePrefix }} {{ $user->surname }} ({{ $user->uziId }})</sub>


            <p>Via via gaan we weten over welke BSN we praten. Nu doen we dat nog niet. Vandaar dat je nog even je BSN moet invullen hier:</p>

            <form method="post" action="{{ route('timeline.fetch') }}">
                @csrf

                <label for="bsn">BSN</label>
                <input type="text" maxlength=9 minlength=8 id="bsn" name="bsn" required>

                <select name="data_domain">
                    <option value="beeldbank">BeeldBank</option>
                </select>
                <button type="submit">Ophalen timeline gegevens</button>
            </form>
        </div>
    </section>
@endsection
