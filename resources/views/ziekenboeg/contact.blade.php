@extends('layouts.ziekenboeg.app')

@section('content')
    <section>
        <div>
            <h1>@lang('Contact')</h1>
            <p class="emphasized">@lang('Do you have any questions?')</p>

            <p>@lang('Mail your questions to <a href="mailto::mail">:mail</a>.', ['mail' => 'info@dezi.nl'])</p>
        </div>
    </section>
@endsection
