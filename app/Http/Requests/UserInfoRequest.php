<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserInfoRequest extends FormRequest
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

            'name' => 'nullable|string',
            'home_address' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'nic' => 'nullable|string',
            'nic_expire_date' => 'nullable|date_format:Y-m-d',
            'passport_no' => 'nullable|string',
            'passport_expire_date' => 'nullable|date_format:Y-m-d',
            'religion' => 'nullable|string',
            'profile_picture' => 'nullable|image',
            'father_name' => 'nullable|string',
            'phone' => 'nullable|integer',
            'email' => 'nullable|string|email',


        ];
    }

    public function messages()
    {
        return [
            'home_address.required' => 'home_address is required!',
            'date_of_birth.required' => 'date_of_birth is required!',
            'nic.required' => 'nic is required!',
            'nic_expire_date.required' => 'nic_expire_date is required!',
            'passport_no.required' => 'passport_no is required!',
            'passport_expire_date.required' => 'passport_expire_date is required!',
            'religion.required' => 'religion is required!',
            'profile_picture.required' => 'profile_picture is required!',
            'father_name.required' => 'father_name is required!',
            'phone.required' => 'phone is required!'

        ];
    }
}
