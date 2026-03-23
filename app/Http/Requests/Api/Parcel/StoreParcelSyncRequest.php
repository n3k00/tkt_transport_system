<?php

namespace App\Http\Requests\Api\Parcel;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreParcelSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tracking_id' => ['required', 'string', 'max:100'],
            'from_town' => ['required', 'integer', 'exists:towns,id'],
            'to_town' => ['required', 'integer', 'exists:towns,id', 'different:from_town'],
            'city_code' => ['required', 'string', 'max:20'],
            'account_code' => ['required', 'string', 'max:255'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'regex:/^09\d{9}$/'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'regex:/^09\d{9}$/'],
            'parcel_type' => ['required', 'string', 'max:255'],
            'number_of_parcels' => ['required', 'integer', 'min:1'],
            'total_charges' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:paid,unpaid'],
            'cash_advance' => ['nullable', 'numeric', 'min:0'],
            'parcel_image_path' => ['nullable', 'string', 'max:500'],
            'remark' => ['nullable', 'string'],
            'status' => ['nullable', 'in:received'],
            'arrived_at' => ['nullable', 'date'],
            'claimed_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedWithDefaults(): array
    {
        return array_merge([
            'cash_advance' => 0,
            'status' => 'received',
        ], $this->validated());
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
