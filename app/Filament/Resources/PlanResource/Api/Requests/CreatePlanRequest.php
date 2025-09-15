<?php

namespace App\Filament\Resources\PlanResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlanRequest extends FormRequest
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
			'service_id' => 'required',
			'name' => 'required',
			'code' => 'required',
			'description' => 'required',
			'days' => 'required',
			'amount' => 'required|numeric',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
