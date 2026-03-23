<?php

namespace App\Http\Requests\Api\Parcel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParcelStatusRequest extends FormRequest
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
            'status' => ['required', 'in:received,dispatched,arrived,claimed'],
            'note' => ['nullable', 'string'],
        ];
    }
}
