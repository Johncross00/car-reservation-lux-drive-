<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('reservation')->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'required', 'date', 'after_or_equal:now'],
            'end_date' => ['sometimes', 'required', 'date', 'after:start_date'],
            'purpose' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:pending,confirmed,finished,refused,cancelled'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');
            $vehicle = $reservation->vehicle;

            if ($this->has('start_date') || $this->has('end_date')) {
                $startDateInput = $this->input('start_date', $reservation->start_date->format('Y-m-d H:i:s'));
                $endDateInput = $this->input('end_date', $reservation->end_date->format('Y-m-d H:i:s'));

                // Parse datetime-local format
                $formats = ['Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];
                $startDate = null;
                $endDate = null;

                foreach ($formats as $format) {
                    $startDate = \DateTime::createFromFormat($format, $startDateInput);
                    if ($startDate !== false) {
                        break;
                    }
                }

                foreach ($formats as $format) {
                    $endDate = \DateTime::createFromFormat($format, $endDateInput);
                    if ($endDate !== false) {
                        break;
                    }
                }

                // Fallback to standard parsing
                if (! $startDate) {
                    try {
                        $startDate = new \DateTime($startDateInput);
                    } catch (\Exception $e) {
                        $validator->errors()->add('start_date', 'Format de date invalide.');
                        return;
                    }
                }

                if (! $endDate) {
                    try {
                        $endDate = new \DateTime($endDateInput);
                    } catch (\Exception $e) {
                        $validator->errors()->add('end_date', 'Format de date invalide.');
                        return;
                    }
                }

                if (! $vehicle->isAvailableForPeriod($startDate, $endDate, $reservation->id)) {
                    $validator->errors()->add('vehicle_id', 'Ce véhicule n\'est pas disponible pour la période sélectionnée.');
                }
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
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'purpose.required' => 'La raison de la réservation est requise.',
            'purpose.max' => 'La raison ne peut pas dépasser 255 caractères.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être l\'un des suivants: pending, confirmed, finished, refused, cancelled.',
        ];
    }
}
