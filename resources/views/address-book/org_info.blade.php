@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>{{ $organization['name'] ?? '' }}</h1>

            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>@lang('Last update')</th>
                    <td><x-company-last-updated :meta="$organization['meta']" /></td>
                </tr>
                </tbody>
            </table>

            <h2>URA & KVK</h2>
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>URA</th>
                    <td><x-company-ura-identifier :identifiers="$organization['identifier'] ?? null" /></td>
                </tr>
                <tr>
                    <th>KvK</th>
                    <td><x-company-kvk-identifier :identifiers="$organization['identifier'] ?? null" /></td>
                </tr>
                </tbody>
            </table>

            <h2>@lang('Contact information organization')</h2>
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>@lang('Name')</th>
                    <td>{{ $organization['name'] ?? '' }}</td>
                </tr>

                @foreach($organization['address'] ?? [] as $address)
                    <tr>
                        <th>@lang('Address') <small>({{ $address['type'] ?? ''}})</small></th>
                        <td>{{ $address['postalCode'] ?? '' }}, {{ $address['city'] ?? ''}} <br>
                            {{ $address['state'] ?? ''}}, {{ $address['country'] ?? ''}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ($endpoints)
                <h2>@lang('Endpoints')</h2>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('payloadMimeTypes')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($endpoints as $endpoint)
                        <tr>
                            <td>{{ $endpoint['name'] ?? '' }}</td>
                            <td>{{ $endpoint['address'] ?? '' }}</td>
                            <td>{{ implode(", ", $endpoint['payloadMimeType']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </section>
@endsection
