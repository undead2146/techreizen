@extends('layouts.app')

@section('content')
    <div class = "row justify-content-center">
        <div class="text-center">
            <div id="images" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <!-- slideshow images -->
                <div class="carousel-inner mx-auto">
                    <div class="carousel-item active">
                        <img src="{{ asset('images/header1.jpg') }}" class="img-fluid " alt="header 1">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('images/header2.jpg') }}" class="img-fluid " alt="header 2">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('images/header3.jpg') }}" class="img-fluid " alt="header 2">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center p-2">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h2>
                        {{ __('You are a visitor!') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
@endsection
