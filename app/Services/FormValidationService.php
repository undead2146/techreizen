<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormValidationService
{
    /**
     * Get validation rules and messages for a specific form
     *
     * @param string $formType
     * @return array
     */
    public function getValidationRules(string $formType): array
    {
        // Define the mapping of form types to their respective validation methods
        $formTypes = [
            'basic_info' => 'getBasicInfoRules',
            'personal_info' => 'getPersonalInfoRules',
            'contact_info' => 'getContactInfoRules',
            'confirmation' => 'getConfirmationRules'
        ];
        $method = 'get' . ucfirst($formType) . 'Rules';
        if (method_exists(object_or_class: $this, method: $method)) {
            return $this->$method();
        }
        
        return [
            'rules' => [],
            'messages' => []
        ];
    }

    /**
     * Validate a form request
     *
     * @param Request $request
     * @param string $formType
     * @return array
     */
    public function validate(Request $request, string $formType): array
    {
        $validation = $this->getValidationRules($formType);
        
        // Create validator instance
        $validator = Validator::make(
            data: $request->all(), 
            rules: $validation['rules'], 
            messages: $validation['messages']
        );
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'validator' => $validator,
                'validated' => []
            ];
        }
        
        return [
            'success' => true,
            'validator' => $validator,
            'validated' => $validator->validated()
        ];
    }
    
    /**
     * Get basic info form validation rules
     *
     * @return array
     */
    private function getBasicInfoRules(): array
    {
        return [
            'rules' => [
                'trip' => 'required|string|max:255',
                'student_number' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[rub]\d{7}$/i'
                ],
                'education' => 'required|string|max:255',
                'major' => 'required|string|max:255',
            ],
            'messages' => [
                'trip.required' => 'Selecteer een reis.',
                'student_number.required' => 'Studentnummer is verplicht.',
                'student_number.regex' => 'Studentnummer moet beginnen met r, u of b en gevolgd worden door 7 cijfers.',
                'education.required' => 'Selecteer een opleiding.',
                'major.required' => 'Selecteer een afstudeerrichting.',
            ]
        ];
    }
    
    /**
     * Get personal info form validation rules
     *
     * @return array
     */
    private function getPersonalInfoRules(): array
    {
        return [
            'rules' => [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|string|in:Man,Vrouw,Anders,Male,Female,Other',
                'nationality' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'place_of_birth' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
            ],
            'messages' => [
                'first_name.required' => 'Voornaam is verplicht.',
                'last_name.required' => 'Achternaam is verplicht.',
                'gender.required' => 'Selecteer een geslacht.',
                'gender.in' => 'Selecteer een geldig geslacht.',
                'nationality.required' => 'Nationaliteit is verplicht.',
                'date_of_birth.required' => 'Geboortedatum is verplicht.',
                'date_of_birth.before' => 'Geboortedatum moet in het verleden zijn.',
                'place_of_birth.required' => 'Geboorteplaats is verplicht.',
                'address.required' => 'Adres is verplicht.',
                'city.required' => 'Stad is verplicht.',
                'country.required' => 'Land is verplicht.',
            ]
        ];
    }
    
    /**
     * Get contact info form validation rules
     *
     * @return array
     */
    private function getContactInfoRules(): array
    {
        return [
            'rules' => [
                'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:travellers,email',
                'secondary_email' => 'nullable|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:travellers,email',
                'phone' => 'required|string|max:15|regex:/^\+?[0-9]{7,15}$/',
                'emergency_contact' => 'required|string|max:15|regex:/^\+?[0-9]{7,15}$/',
                'optional_emergency_contact' => 'nullable|string|max:15|regex:/^\+?[0-9]{7,15}$/',
                'medical_info' => 'required|in:yes,no',
                'medical_details' => 'required_if:medical_info,yes|nullable|string',
            ],
            'messages' => [
                'email.required' => 'E-mailadres is verplicht.',
                'email.email' => 'Voer een geldig e-mailadres in.',
                'email.regex' => 'Het e-mailadres moet een geldig formaat hebben.',
                'email.unique' => 'Dit e-mailadres is al in gebruik.',
                'secondary_email.email' => 'Voer een geldig tweede e-mailadres in.',
                'secondary_email.regex' => 'Het tweede e-mailadres moet een geldig formaat hebben.',
                'secondary_email.unique' => 'Dit tweede e-mailadres is al in gebruik.',
                'phone.required' => 'Telefoonnummer is verplicht.',
                'phone.regex' => 'Het telefoonnummer moet een geldig formaat hebben (bijv. +32412345678).',
                'emergency_contact.required' => 'Noodnummer 1 is verplicht.',
                'emergency_contact.regex' => 'Het noodnummer moet een geldig formaat hebben (bijv. +32412345678).',
                'optional_emergency_contact.regex' => 'Het optionele noodnummer moet een geldig formaat hebben (bijv. +32412345678).',
                'medical_info.required' => 'Geef aan of er medische informatie is.',
                'medical_details.required_if' => 'Vul de medische details in als u "Ja" selecteert.',
            ]
        ];
    }
    
    /**
     * Get confirmation form validation rules
     *
     * @return array
     */
    private function getConfirmationRules(): array
    {
        return [
            'rules' => [
                'terms_accepted' => 'required|accepted',
            ],
            'messages' => [
                'terms_accepted.required' => 'U moet de algemene voorwaarden accepteren.',
                'terms_accepted.accepted' => 'U moet de algemene voorwaarden accepteren.',
            ]
        ];
    }
}
