<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('products')->ignore($productId),
            ],
            'category_id' => 'required|exists:categories,id',
            'current_quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'slug.max' => 'O slug do produto não pode ter mais de 255 caracteres.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens, sem espaços ou caracteres especiais.',
            'slug.unique' => 'Este slug já está em uso por outro produto.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'current_quantity.required' => 'A quantidade em estoque é obrigatória.',
            'current_quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'current_quantity.min' => 'A quantidade não pode ser negativa.',
            'unit_price.required' => 'O preço unitário é obrigatório.',
            'unit_price.numeric' => 'O preço deve ser um número válido.',
            'unit_price.min' => 'O preço deve ser maior que zero.',
            'image.image' => 'O arquivo deve ser uma imagem válida.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou gif.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',
        ];
    }
}
