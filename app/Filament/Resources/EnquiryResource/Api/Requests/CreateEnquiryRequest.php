<?php

namespace App\Filament\Resources\EnquiryResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEnquiryRequest extends FormRequest
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
			'user_id' => 'required',
			'name' => 'required',
			'email' => 'required',
			'contact' => 'required',
			'date' => 'required|date',
			'gender' => 'required',
			'dob' => 'required|date',
			'address' => 'required|string',
			'country' => 'required',
			'city' => 'required',
			'state' => 'required',
			'pincode' => 'required|string',
			'status' => 'required',
			'interested_in' => 'required',
			'source' => 'required',
			'goal' => 'required',
			'start_by' => 'required|date',
			'deleted_at' => 'required'
		];
    }
}
