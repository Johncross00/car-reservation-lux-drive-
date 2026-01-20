<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date', 'after_or_equal:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'purpose' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $vehicle = Vehicle::find($this->vehicle_id);

            if (! $vehicle) {
                return;
            }

            if (! $vehicle->is_available) {
                $validator->errors()->add('vehicle_id', 'Ce véhicule n\'est pas disponible.');
            }

            // Parse datetime-local format (YYYY-MM-DDTHH:mm or YYYY-MM-DDTHH:mm:ss)
            $startDate = null;
            $endDate = null;

            // Try different formats for datetime-local input
            $formats = ['Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];

            foreach ($formats as $format) {
                $startDate = \DateTime::createFromFormat($format, $this->start_date);
                if ($startDate !== false) {
                    break;
                }
            }

            foreach ($formats as $format) {
                $endDate = \DateTime::createFromFormat($format, $this->end_date);
                if ($endDate !== false) {
                    break;
                }
            }

            // If parsing failed, try standard DateTime constructor
            if (! $startDate) {
                try {
                    $startDate = new \DateTime($this->start_date);
                } catch (\Exception $e) {
                    $validator->errors()->add('start_date', 'Format de date invalide.');
                    return;
                }
            }

            if (! $endDate) {
                try {
                    $endDate = new \DateTime($this->end_date);
                } catch (\Exception $e) {
                    $validator->errors()->add('end_date', 'Format de date invalide.');
                    return;
                }
            }

            if (! $vehicle->isAvailableForPeriod($startDate, $endDate)) {
                $validator->errors()->add('vehicle_id', 'Ce véhicule n\'est pas disponible pour la période sélectionnée.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Veuillez sélectionner un véhicule.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'purpose.required' => 'La raison de la réservation est requise.',
            'purpose.max' => 'La raison ne peut pas dépasser 255 caractères.',
        ];
    }
}
