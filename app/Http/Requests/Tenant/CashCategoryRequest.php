<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\CashCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashCategoryRequest extends FormRequest
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
        $categoryId = $this->route('cashCategory')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cash_categories', 'name')
                    ->ignore($categoryId)
                    ->where('type', $this->input('type'))
            ],
            'type' => 'required|in:' . implode(',', array_keys(CashCategory::types())),
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Kategori dengan nama ini sudah ada untuk jenis yang dipilih',
            'type.required' => 'Jenis kategori harus diisi',
            'type.in' => 'Jenis kategori harus berupa pemasukan atau pengeluaran',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'type' => 'Jenis',
            'description' => 'Keterangan',
        ];
    }
}
