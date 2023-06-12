<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAvailabilityListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fromDate'          => 'nullable|date_format:Y-m-d',
            'toDate'            => 'nullable|date_format:Y-m-d',
            'propertyId'        => 'nullable|int',
        ];
    }
}
