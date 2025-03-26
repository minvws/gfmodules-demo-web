@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>@lang('Search Address Book')</h1>

            <form action="{{ route('address-book') }}" method="GET" class="layout-form">
                <fieldset>
                    <div>
                        <label for="address-book-search-name">Zoeken op naam</label>
                        <div>
                            @error('name')
                            <p class="error" id="address-book-search-name-error-message">
                                <span>Foutmelding:</span> {{ $message }}
                            </p>
                            @enderror
                            <input
                                id="address-book-search-name"
                                name="name"
                                type="text"
                                aria-describedby="address-book-search-name-error-message"
                                value="{{ request()->query('name') ?? old('name') }}"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="address-book-search-ura">Zoeken op ura</label>
                        <div>
                            @error('ura')
                            <p class="error" id="address-book-search-ura-error-message">
                                <span>Foutmelding:</span> {{ $message }}
                            </p>
                            @enderror
                            <input
                                id="address-book-search-ura"
                                name="ura"
                                type="text"
                                maxlength="8"
                                aria-describedby="address-book-search-ura-error-message"
                                value="{{ request()->query('ura') ?? old('ura') }}"
                            />
                        </div>
                    </div>

                    <button type="submit">Zoeken</button>
                </fieldset>
            </form>
        </div>
    </section>
@endsection
