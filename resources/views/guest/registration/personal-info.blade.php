@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('Registratie - Persoonlijke Informatie') }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <x-registration-progress :currentStep="2" />

                        <form method="POST" action="{{ route('guest.registration.personal-info.submit') }}">
                            @csrf

                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">{{ __('Persoonlijke Informatie') }}</h5>
                            </div>

                            <div class="row mb-3">
                                <label for="first_name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Voornaam*') }}</label>
                                <div class="col-md-6">
                                    <input id="first_name" type="text"
                                        class="form-control @error('first_name') is-invalid @enderror" name="first_name"
                                        value="{{ old('first_name', $registration->first_name ?? '') }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="last_name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Achternaam*') }}</label>
                                <div class="col-md-6">
                                    <input id="last_name" type="text"
                                        class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                                        value="{{ old('last_name', $registration->last_name ?? '') }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="gender"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Geslacht*') }}</label>
                                <div class="col-md-6">
                                    <select id="gender" class="form-control @error('gender') is-invalid @enderror"
                                        name="gender" required>
                                        <option value="">{{ __('-- Selecteer Geslacht --') }}</option>
                                        <option value="Man" {{ (old('gender', $registration->gender ?? '') == 'Man') ? 'selected' : '' }}>Man</option>
                                        <option value="Vrouw" {{ (old('gender', $registration->gender ?? '') == 'Vrouw') ? 'selected' : '' }}>Vrouw</option>
                                        <option value="Anders" {{ (old('gender', $registration->gender ?? '') == 'Anders') ? 'selected' : '' }}>Anders</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="date_of_birth"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Geboortedatum*') }}</label>
                                <div class="col-md-6">
                                    <input id="date_of_birth" type="date"
                                        class="form-control @error('date_of_birth') is-invalid @enderror"
                                        name="date_of_birth"
                                        value="{{ old('date_of_birth', $registration->date_of_birth ?? '') }}" required>
                                    @error('date_of_birth')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="place_of_birth"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Geboorteplaats*') }}</label>
                                <div class="col-md-6">
                                    <input id="place_of_birth" type="text"
                                        class="form-control @error('place_of_birth') is-invalid @enderror"
                                        name="place_of_birth"
                                        value="{{ old('place_of_birth', $registration->place_of_birth ?? '') }}" required>
                                    @error('place_of_birth')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="nationality"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Nationaliteit*') }}</label>
                                <div class="col-md-6">
                                    <input id="nationality" type="text"
                                        class="form-control" name="nationality"
                                        value="{{ old('nationality', $registration->nationality ?? '') }}" required>
                                </div>
                            </div>
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2">{{ __('Adres Informatie') }}</h5>
                                </div>
                            <div class="row mb-3">
                                <label for="address" class="col-md-4 col-form-label text-md-end">{{ __('Adres*') }}</label>
                                <div class="col-md-6">
                                    <input id="address" type="text"
                                        class="form-control @error('address') is-invalid @enderror" name="address"
                                        value="{{ old('address', $registration->address ?? '') }}" required>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="city" class="col-md-4 col-form-label text-md-end">{{ __('Gemeente*') }}</label>
                                <div class="col-md-6">
                                    <div class="city-selector-wrapper">
                                        <x-city-selector 
                                            :selected-city="old('city', $registration->city ?? '')"
                                            :selected-postal-code="old('postcode', $registration->postcode ?? '')"
                                            :has-error="$errors->has('city')" 
                                            required
                                        />
                                    </div>
                                    <div class="form-text">Zoek een gemeente of voeg er een toe</div>

                                    @error('city')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>




                            <div class="row mb-3">
                                <label for="country" class="col-md-4 col-form-label text-md-end">{{ __('Land*') }}</label>
                                <div class="col-md-6">
                                    <input id="country" type="text"
                                        class="form-control @error('country') is-invalid @enderror" name="country"
                                        value="{{ old('country', $registration->country ?? '') }}" required>
                                    @error('country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <a type="submit" href="{{ route('guest.registration.basic-info') }}" class="btn btn-secondary me-2">
                                        {{ __('Vorige') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Volgende') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success notification -->
    <div 
        id="cityAddedToast" 
        class="toast position-fixed bottom-0 end-0 m-3" 
        role="alert" 
        aria-live="assertive" 
        aria-atomic="true"
        data-bs-delay="3000"
    >
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Succes</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Gemeente succesvol toegevoegd!
        </div>
    </div>

    <!-- City Found notification -->
    <div 
        id="cityFoundToast" 
        class="toast position-fixed bottom-0 end-0 m-3" 
        role="alert" 
        aria-live="assertive" 
        aria-atomic="true"
        data-bs-delay="3000"
    >
        <div class="toast-header bg-info text-white">
            <strong class="me-auto">Informatie</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Deze gemeente bestaat al en is geselecteerd.
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Listen for city-added event to show success toast
        window.addEventListener('city-added', event => {
            const toastEl = document.getElementById('cityAddedToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
        
        // Listen for city-found event to show info toast
        window.addEventListener('city-found', event => {
            const toastEl = document.getElementById('cityFoundToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
        
        // Fix for form submission with city selector
        const form = document.querySelector('form[action="{{ route('guest.registration.personal-info.submit') }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Check specifically for the city field
                const cityInput = form.querySelector('input[name="city"]');
                if (cityInput && cityInput.hasAttribute('required') && !cityInput.value.trim()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Make the city selector appear invalid
                    const cityWrapper = cityInput.closest('.city-selector-wrapper');
                    if (cityWrapper) {
                        const searchInput = cityWrapper.querySelector('.search-input');
                        if (searchInput) {
                            searchInput.classList.add('is-invalid');
                            searchInput.focus();
                        }
                    }
                    
                    // Show validation message
                    alert('Selecteer een gemeente voordat u verder gaat.');
                    return false;
                }
                
                // Disable all inputs in modals that might be focusable
                document.querySelectorAll('.city-modal-container input').forEach(input => {
                    input.disabled = true;
                });
            });
        }
    });
</script>

<style>
    .city-selector-wrapper {
        position: relative;
    }
    
    [x-cloak] { 
        display: none !important; 
    }
</style>
@endpush
