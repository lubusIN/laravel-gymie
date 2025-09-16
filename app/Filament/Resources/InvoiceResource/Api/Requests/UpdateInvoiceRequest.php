<?php

namespace App\Filament\Resources\InvoiceResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
			'number' => 'required',
			'subscription_id' => 'required',
			'date' => 'required|date',
			'due_date' => 'required|date',
			'payment_method' => 'required',
			'discount' => 'required|numeric',
			'tax' => 'required|numeric',
			'discount_amount' => 'required|numeric',
			'discount_note' => 'required',
			'paid_amount' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'due_amount' => 'required|numeric',
			'subscription_fee' => 'required|numeric',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
