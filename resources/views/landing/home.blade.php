@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>Llama grazing ground</h1>

            {{ $user->initials }} {{ $user->surnamePrefix }} {{ $user->surname }} ({{ $user->uziId }})

            <a href="{{ route('landing.logout') }}">Logout</a>

            <img src="{{ asset('img/llama-grazing.jpg') }}" alt="Llama grazing">
        </div>
    </section>
@endsection
