@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Wachtwoord Herstellen') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-3">Vul uw login nummer in, en wij sturen een wachtwoord herstellink naar het e-mailadres dat aan uw account is gekoppeld.</p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="login" class="col-md-4 col-form-label text-md-end">{{ __('Login nummer') }}</label>

                            <div class="col-md-6">
                                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="login" autofocus>

                                @error('login')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">Voer uw r-, u- of b-nummer in (bijv. r0123456).</small>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verstuur herstellink') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginInput = document.getElementById('login');
        const form = loginInput.closest('form');
        
        // Validate on form submission
        form.addEventListener('submit', function (event) {
            const pattern = /^[rub]\d{7}$/i;  // Case insensitive regex
            
            // Reset any previous visual styling
            loginInput.classList.remove('is-invalid');
            
            // Validate the student number format
            if (!pattern.test(loginInput.value)) {
                event.preventDefault();
                loginInput.classList.add('is-invalid');
                
                // Add error message
                const feedback = document.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.innerHTML = '<strong>Het studentnummer moet beginnen met r, u of b en gevolgd worden door 7 cijfers.</strong>';
                } else {
                    const errorDiv = document.createElement('span');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerHTML = '<strong>Het studentnummer moet beginnen met r, u of b en gevolgd worden door 7 cijfers.</strong>';
                    loginInput.parentNode.appendChild(errorDiv);
                }
                
                // Focus on the field with error
                loginInput.focus();
            }
        });
        
        // Remove invalid class on input to provide immediate feedback
        loginInput.addEventListener('input', function () {
            const pattern = /^[rub]\d{7}$/i;
            if (pattern.test(loginInput.value)) {
                loginInput.classList.remove('is-invalid');
            }
        });
    });
</script>
@endsection
