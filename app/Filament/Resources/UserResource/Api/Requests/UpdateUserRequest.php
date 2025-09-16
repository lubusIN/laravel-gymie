<?php

namespace App\Filament\Resources\UserResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
			'photo' => 'required',
			'name' => 'required',
			'email' => 'required',
			'status' => 'required',
			'email_verified_at' => 'required',
			'password' => 'required',
			'contact' => 'required',
			'dob' => 'required|date',
			'gender' => 'required',
			'address' => 'required',
			'country' => 'required',
			'city' => 'required',
			'state' => 'required',
			'pincode' => 'required',
			'deleted_at' => 'required',
			'remember_token' => 'required'
		];
    }
}
