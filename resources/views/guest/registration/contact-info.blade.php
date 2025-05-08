@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('Registratie - Contactgegevens') }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <x-registration-progress :currentStep="3" />

                        <form method="POST" action="{{ route('guest.registration.contact-info.submit') }}">
                            @csrf

                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">{{ __('Contactgegevens') }}</h5>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-mailadres*') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email', $registration->email ?? '') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            @if (str_starts_with(session('guest_registration.student_number'), 'r'))
                            <div class="row mb-3">
                                <label for="secondary_email" class="col-md-4 col-form-label text-md-end">{{ __('E-mailadres 2') }}</label>
                                <div class="col-md-6">
                                    <input id="secondary_email" type="text" class="form-control @error('secondary_email') is-invalid @enderror" 
                                           name="secondary_email" value="{{ old('secondary_email', $registration->secondary_email ?? '') }}">
                                    @if ($errors->has('secondary_email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('secondary_email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Telefoonnummer*') }}</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" value="{{ old('phone', $registration->phone ?? '') }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="emergency_contact" class="col-md-4 col-form-label text-md-end">{{ __('Noodnummer 1*') }}</label>
                                <div class="col-md-6">
                                    <input id="emergency_contact" type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                           name="emergency_contact" value="{{ old('emergency_contact', $registration->emergency_contact ?? '') }}" required>
                                    @error('emergency_contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="optional_emergency_contact" class="col-md-4 col-form-label text-md-end">{{ __(' Noodnummer 2') }}</label>
                                <div class="col-md-6">
                                    <input id="optional_emergency_contact" type="text" class="form-control @error('optional_emergency_contact') is-invalid @enderror" 
                                           name="optional_emergency_contact" value="{{ old('optional_emergency_contact', $registration->optional_emergency_contact ?? '') }}">
                                    @error('optional_emergency_contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">{{ __('Medische Gegevens') }}</h5>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-12 col-form-label text-md-start mb-2">{{ __('Zijn er medische gegevens die belangrijk zijn voor de begeleiders? (Allergiën, ziektes, medicatie, ...)') }}</label>
                                <div class="col-md-6 offset-md-4 d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="medical_info" id="medical_info_no" value="no" {{ old('medical_info', ($registration->medical_details ?? '') ? '' : 'checked') }}>
                                        <label class="form-check-label" for="medical_info_no">{{ __('Nee') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="medical_info" id="medical_info_yes" value="yes" {{ old('medical_info', ($registration->medical_details ?? '') ? 'checked' : '') }}>
                                        <label class="form-check-label" for="medical_info_yes">{{ __('Ja') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="medical_details" class="col-md-4 col-form-label text-md-end">{{ __('Medische Details') }}</label>
                                <div class="col-md-6">
                                    <textarea id="medical_details" class="form-control @error('medical_details') is-invalid @enderror" 
                                        name="medical_details" rows="4" style="background-color: #e9ecef;" readonly>{{ old('medical_details', $registration->medical_details ?? '') }}</textarea>
                                    @error('medical_details')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <a href="{{ route('guest.registration.personal-info') }}" class="btn btn-secondary me-2">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const medicalInfoYes = document.getElementById('medical_info_yes');
            const medicalInfoNo = document.getElementById('medical_info_no');
            const medicalDetails = document.getElementById('medical_details');

            // Set initial state based on old input or previous value
            if (medicalInfoYes.checked) {
                medicalDetails.style.backgroundColor = 'white';
                medicalDetails.removeAttribute('readonly');
            }

            function toggleMedicalDetails() {
                if (medicalInfoYes.checked) {
                    medicalDetails.style.backgroundColor = 'white';
                    medicalDetails.removeAttribute('readonly');
                } else {
                    medicalDetails.style.backgroundColor = '#e9ecef';
                    medicalDetails.setAttribute('readonly', true);
                    medicalDetails.value = ''; // Clear the text area when "Nee" is selected
                }
            }

            // Initialize the form state correctly when page loads with validation errors
            toggleMedicalDetails();

            medicalInfoYes.addEventListener('change', toggleMedicalDetails);
            medicalInfoNo.addEventListener('change', toggleMedicalDetails);
        });
    </script>
@endsection
