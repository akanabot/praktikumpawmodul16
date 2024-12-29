@extends('layouts.wonav')

@section('content')
    <div class="container-sm d-flex align-items-center justify-content-center min-vh-100" style="background-color: #0d6efd;">
        <div class="col-xl-4">
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="text-center mb-4 mt-3   ">
                            <div class="mb-3">
                                <!-- Hexagon Icon -->
                                <i class="bi bi-hexagon-fill fs-1 text-primary"></i>
                            </div>

                            <div class="mb-4">
                                <h4 class="fw mb-5">Employee Data Master</h4>
                            </div>
                        </div>
                        <hr>
                        <!-- Email Input -->
                        <div class="mb-3">
                            <input id="email" type="email"
                                class="form-control py-2 @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" placeholder="Enter Your Email" required autocomplete="email"
                                autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <input id="password" type="password"
                                class="form-control py-2 @error('password') is-invalid @enderror" name="password"
                                placeholder="Enter Your Password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <hr>
                        <!-- Login Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Tambahkan CSS berikut di bagian head atau file CSS terpisah -->
<style>
    .min-vh-100 {
        min-height: 100vh;
    }

    .card {
        border: none;
    }

    .form-control {
        border-radius: 5px;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }

    .btn-primary {
        border-radius: 5px;
    }
</style>
