@extends('layouts.app')

@section('content')
    <section>
        <div>
            <h1>{{ $organization['name'] ?? '' }}</h1>


            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>Laatste update</th>
                    <td>{{ $organization['meta']['lastUpdated'] ?? '' }}</td>
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
                    <td></td>
                </tr>
                </tbody>
            </table>

            <h2>Contactgegevens organisatie</h2>
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>Name</th>
                    <td>{{ $organization['name'] ?? '' }}</td>
                </tr>

                @foreach($organization['address'] ?? [] as $address)
                    <tr>
                        <th>Address <small>({{ $address['type'] ?? ''}})</small></th>
                        <td>{{ $address['postalCode'] ?? '' }}, {{ $address['city'] ?? ''}} <br>
                            {{ $address['state'] ?? ''}}, {{ $address['country'] ?? ''}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>


            @if ($endpoints)
                <h2>Endpoints</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>payloadMimeTypes</th>
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
