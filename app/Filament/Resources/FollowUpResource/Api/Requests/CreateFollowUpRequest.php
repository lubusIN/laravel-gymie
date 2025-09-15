<?php

namespace App\Filament\Resources\FollowUpResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFollowUpRequest extends FormRequest
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
			'enquiry_id' => 'required',
			'user_id' => 'required',
			'schedule_date' => 'required|date',
			'method' => 'required',
			'outcome' => 'required',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
