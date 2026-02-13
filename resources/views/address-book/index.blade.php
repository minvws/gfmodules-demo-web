@extends('layouts.app')

@section('content')
    <section>
        <div class="content-wrapper">
            <h1>@lang('Search Address Book')</h1>

            <form action="{{ route('address-book') }}" method="GET" class="layout-form">
                <fieldset>
                    <div>
                        <label for="address-book-search-name">@lang('Search on name')</label>
                        <div>
                            @error('name')
                            <p class="error" id="address-book-search-name-error-message">
                                <span>@lang('Error'):</span> {{ $message }}
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
                        <label for="address-book-search-ura">@lang('Search on ura')</label>
                        <div>
                            @error('ura')
                            <p class="error" id="address-book-search-ura-error-message">
                                <span>@lang('Error'):</span> {{ $message }}
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

                </fieldset>
                <button type="submit">@lang('Search')</button>
            </form>
        </div>
    </section>
    <section>
        <div class="content-wrapper">
            <h2>@lang('Results')</h2>

            @if($error)
                <p class="error" aria-label="{{__('Error') }}">
                    <span>@lang('Error'):</span> {{ $error }}
                </p>
            @endif

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>@lang('Organization')</th>
                    <th>@lang('URA')</th>
                    <th>@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($result->organizations ?? [] as $organization)
                    <tr>
                        <td>{{ $organization['name'] ?? '' }}</td>
                        <td><x-company-ura-identifier :identifiers="$organization['identifier'] ?? null" /></td>
                        <td><a href="{{ route('address-book.org-info', ['ref' => $organization['id'] ]) }}">@lang('View')</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">@lang('No results found')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if($result && $result->total)
                <p>{{ trans_choice(':number organisation found.|A total of :number organizations have been found.', $result->total, ['number' => $result->total]) }}</p>
            @endif

            @if($result)
                <nav class="pagination" aria-label="@lang('Pagination')">
                    @if ($result->hasPreviousPage())
                    <a class="adjacent previous" href="{{ route('address-book', $result->previousPageQuery) }}" aria-label="@lang('Previous Page')">@lang('Previous Page')</a>
                    @else
                    <span class="disabled adjacent previous" aria-label="@lang('Previous Page')">@lang('Previous Page')</span>
                    @endif

                    @if ($result->hasNextPage())
                        <a class="adjacent next" href="{{ route('address-book', $result->nextPageQuery) }}" aria-label="@lang('Next Page')">@lang('Next Page')</a>
                    @else
                        <span class="disabled adjacent next" aria-label="@lang('Next Page')">@lang('Next Page')</span>
                    @endif
                </nav>
            @endif
        </div>
    </section>
@endsection
