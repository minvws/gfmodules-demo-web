@if(config('app.env') === "production")
<a href="/" class="ro-logo" aria-label="{{__('Rijksoverheid logo, go to the homepage')}}">
    <img src="{{ asset('img/ro-logo.svg') }}" alt="Logo Rijksoverheid">Rijksoverheid
</a>
@else
<a href="/" class="ro-logo" aria-label="{{__('Rijksoverheid logo, go to the homepage')}}">
    <img src="{{ asset('img/pUZI-hidden.svg') }}" alt="Logo Rijksoverheid">Mewisterie van pUZI UwU
</a>
@endif
