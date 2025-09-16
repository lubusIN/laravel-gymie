<?php

namespace App\Filament\Resources\MemberResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
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
			'code' => 'required',
			'name' => 'required',
			'email' => 'required',
			'contact' => 'required',
			'emergency_contact' => 'required',
			'health_issue' => 'required',
			'gender' => 'required',
			'dob' => 'required|date',
			'address' => 'required|string',
			'country' => 'required',
			'city' => 'required',
			'state' => 'required',
			'pincode' => 'required|string',
			'source' => 'required',
			'goal' => 'required',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
