@extends('layouts.guest')

@section('content')
    @if (session()->has('error'))
        <section role="alert" class="error no-print" aria-label="{{ __('error') }}">
            <div>
                <h4>{{ session('error') }}</h4>
                <p>{{ session('error_description') }}</p>
            </div>
        </section>
    @endif

    <section>
        <div>
            <h1>Landingspagina</h1>

            <p>Here be llamas...</p>

            <ul class="external-login">
                <li><a href="{{ route('oidc.login') }}"><img src="{{ asset('img/login-methods/signin-method-logo.png') }}" alt="">@lang('Login with')
                        Dezi-Online <i class="icon icon-chevron-right icon-small"></i></a></li>
            </ul>
        </div>
    </section>
@endsection
