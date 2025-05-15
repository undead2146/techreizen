@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Registratie - Basisgegevens') }}</span>
                    </div>
                </div>

                <div class="card-body">
                    <x-registration-progress :currentStep="1" />
                    
                    <form method="POST" action="{{ route('guest.registration.basic-info.submit') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">{{ __('Reis & Opleiding Details') }}</h5>
                        </div>

                        <div class="row mb-3">
                            <label for="trip" class="col-md-4 col-form-label text-md-end">{{ __('Reis*') }}</label>
                            <div class="col-md-6">
                                <select id="trip" class="form-control @error('trip') is-invalid @enderror" 
                                       name="trip" required>
                                    <option value="">-- Selecteer Reis --</option>
                                    @foreach($trips as $trip)
                                        <option value="{{ $trip->name }}" {{ old('trip', $registration->trip) == $trip->name ? 'selected' : '' }}>
                                            {{ $trip->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- HIER CHECKEN OP R, U, B NUMMER -->
                        <div class="row mb-3">
                            <label for="student_number" class="col-md-4 col-form-label text-md-end">{{ __('Login nummer*') }}</label>
                            <div class="col-md-6">
                                <input id="student_number" type="text" class="form-control @error('student_number') is-invalid @enderror" 
                                    name="student_number" value="{{ old('student_number', $registration->student_number) }}" required>
                                @error('student_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @else
                                    <small class="form-text text-muted">Moet beginnen met r, u of b en gevolgd worden door 7 cijfers.</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Opleiding Details (worden verborgen als studentnummer niet met 'r' begint) -->
                        <div id="education-fields" style="display: none;">
                            <div class="row mb-3">
                                <label for="education" class="col-md-4 col-form-label text-md-end">{{ __('Opleiding*') }}</label>
                                <div class="col-md-6">
                                    <select id="education" class="form-control @error('education') is-invalid @enderror" 
                                        name="education">
                                        <option value="">-- Selecteer Opleiding --</option>
                                        @foreach($educations as $education)
                                            <option value="{{ $education->id }}" {{ old('education', $registration->education) == $education->id ? 'selected' : '' }}>
                                                {{ $education->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('education')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="major" class="col-md-4 col-form-label text-md-end">{{ __('Afstudeerrichting*') }}</label>
                                <div class="col-md-6">
                                    <select id="major" class="form-control @error('major') is-invalid @enderror" 
                                        name="major">
                                        <option value="">-- Selecteer Afstudeerrichting --</option>
                                        @foreach($majors as $major)
                                            <option value="{{ $major->name }}" {{ old('major', $registration->major) == $major->name ? 'selected' : '' }}>
                                                {{ $major->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('major')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Volgende') }}
                                </button>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const studentNumberInput = document.getElementById('student_number');
                                const educationFields = document.getElementById('education-fields');

                                // Functie om velden te tonen of te verbergen
                                function toggleEducationFields() {
                                    const studentNumber = studentNumberInput.value.trim().toLowerCase();
                                    if (studentNumber.startsWith('r')) {
                                        educationFields.style.display = 'block'; // Toon velden
                                    } else {
                                        educationFields.style.display = 'none'; // Verberg velden
                                    }
                                }

                                // Controleer bij het laden van de pagina
                                toggleEducationFields();

                                // Controleer bij elke wijziging in het studentnummer
                                studentNumberInput.addEventListener('input', toggleEducationFields);
                            });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentNumberInput = document.getElementById('student_number');
        const form = studentNumberInput.closest('form');
        
        // Only validate on form submission
        form.addEventListener('submit', function (event) {
            const pattern = /^[rub]\d{7}$/;  // Case insensitive regex
            
            // Reset any previous visual styling
            studentNumberInput.classList.remove('is-invalid');
            
            // Only validate client-side if there's no server validation error already
            if (!studentNumberInput.classList.contains('is-invalid') && !pattern.test(studentNumberInput.value)) {
                event.preventDefault();
                studentNumberInput.classList.add('is-invalid');
                
                // Focus on the field with error
                studentNumberInput.focus();
            }
        });
        
        // Remove invalid class on input to provide immediate feedback
        studentNumberInput.addEventListener('input', function () {
            const pattern = /^[rub]\d{7}$/;
            if (pattern.test(studentNumberInput.value)) {
                studentNumberInput.classList.remove('is-invalid');
            }
        });

        const educationSelect = document.getElementById('education');
        const majorSelect = document.getElementById('major');

        educationSelect.addEventListener('change', function () {
            const educationId = this.value;

            // Clear the majors dropdown
            majorSelect.innerHTML = '<option value="">-- Selecteer Afstudeerrichting --</option>';

            if (educationId) {
                // Fetch majors for the selected education
                fetch(`/majors/${educationId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(major => {
                            const option = document.createElement('option');
                            option.value = major.name;
                            option.textContent = major.name;
                            majorSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching majors:', error));
            }
        });
    });
</script>
