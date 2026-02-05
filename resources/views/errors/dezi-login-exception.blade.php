@extends('layouts.guest')

@section('content')
    <section class="layout-authentication">
        <div>
            <div class="error" role="group" aria-label="{{__('Error') }}">
                <span>@lang('Error')</span>
                <h1>
                    @lang('Error')
                    @lang(403)
                </h1>
                <p>@lang('Something went wrong during the Dezi login process.')</p>

                <a href="{{ route('index') }}" class="button">
                    @lang('Go back to homepage')
                </a>
            </div>
        </div>
    </section>
@endsection
