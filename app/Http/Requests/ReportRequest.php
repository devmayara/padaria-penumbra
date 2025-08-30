<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date|before_or_equal:end_date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'start_date.before_or_equal' => 'A data de início deve ser menor ou igual à data final e não pode ser futura.',
            'end_date.required' => 'A data final é obrigatória.',
            'end_date.date' => 'A data final deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data final deve ser maior ou igual à data de início.',
            'end_date.before_or_equal' => 'A data final não pode ser futura.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'data de início',
            'end_date' => 'data final',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Se não houver datas, definir como mês atual
        if (!$this->has('start_date')) {
            $this->merge([
                'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d')
            ]);
        }

        if (!$this->has('end_date')) {
            $this->merge([
                'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d')
            ]);
        }
    }
}
