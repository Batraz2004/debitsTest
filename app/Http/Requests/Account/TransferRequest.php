<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'from_user_id' => ['required', 'numeric', 'gt:0'],
            'to_user_id' => ['required', 'numeric', 'gt:0'],
            'amount' => ['required','decimal:2'],
            'comment' => ['required', 'string'],
        ];
    }

    public function getData()
    {
        $data = $this->only([
            'user_id',
            'user_to_id',
            'amount',
            'comment'
        ]);

        return $data;
    }
}
