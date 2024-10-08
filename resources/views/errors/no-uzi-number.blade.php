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
                <p>U zit momenteel niet in de Dezi-pilot.</p>

                <form method="post" action="{{ route('ziekenboeg.users.logout') }}" class="inline">
                    @csrf
                    <input type="submit" value="{{ __('Logout') }}" />
                </form>
            </div>
        </div>
    </section>
@endsection
