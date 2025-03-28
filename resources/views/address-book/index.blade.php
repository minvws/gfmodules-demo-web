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
    <section>
        <div>
            <h2>Resultaten</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>Organisatie</th>
                    <th>URA</th>
                    <th>Actie</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($result->organizations as $organization)
                    <tr>
                        <td>{{ $organization['name'] ?? '' }}</td>
                        <td>{{ (array_values(array_filter($organization['identifier'] ?? [], fn($identifier) => ($identifier['system'] ?? '') === 'http://fhir.nl/fhir/NamingSystem/ura') ?? []))[0]['value'] ?? '' }}</td>
                        <td><a href="{{route('address-book.org-info', ['ref' => $organization['id'] ])}}">Bekijken</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Geen resultaten gevonden</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if ($result->total)
                <p>A total of {{ $result->total }} organizations have been found.</p>
            @endif

            <nav class="pagination" aria-label="Paginering">
                @if ($result->hasPreviousPage())
                <a class="adjacent previous" href="{{ route('address-book', $result->previousPageQuery) }}" aria-label="Vorige pagina">Vorige pagina</a>
                @else
                <span class="disabled adjacent previous" aria-label="Vorige pagina">Vorige pagina</span>
                @endif

                @if ($result->hasNextPage())
                    <a class="adjacent next" href="{{ route('address-book', $result->nextPageQuery) }}" aria-label="Volgende pagina">Volgende pagina</a>
                @else
                    <span class="disabled adjacent next" aria-label="Volgende pagina">Volgende pagina</span>
                @endif
            </nav>
        </div>
    </section>
@endsection
