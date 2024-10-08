@extends('layouts.template.base-sidebar-next-to-page')
@push('styles')
    @vite(['resources/css/ziekenboeg.scss', 'resources/js/app.js'])
@endpush

@section('header')
    <x-header-ziekenboeg logo-component="header-logo-ziekenboeg"
                         navigation-component="header-navigation-bar-ziekenboeg"/>
@endsection

@section('footer')
    <x-footer-ziekenboeg/>
@endsection
