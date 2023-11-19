<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppointmentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'consultant_name' => 'required',
            'agenda' => 'required',
            'status' => 'nullable'


        ];
    }


    public function messages()
    {

        return [
            'date.required' => 'The date field is required.',
            'time.required' => 'The time field is required.',
            'consultant_name.required' => 'The consultant name field is required.',
            'agenda.required' => 'The agenda field is required.',
            'status.required' => 'The status field is required'


        ];
    }
}
