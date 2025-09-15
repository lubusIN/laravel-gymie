<?php

namespace App\Filament\Resources\SubscriptionResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
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
			'member_id' => 'required',
			'plan_id' => 'required',
			'start_date' => 'required|date',
			'end_date' => 'required|date',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
