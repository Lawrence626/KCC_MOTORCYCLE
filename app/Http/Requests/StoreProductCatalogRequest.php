<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductCatalogRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_name' => 'nullable|string|max:255',
            'brand' => 'required|string|max:255',
            'product_description' => 'required|string|exists:product_descriptions,name',
            'sku' => 'required|string|max:50|unique:product_catalog,sku',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'warehouse' => 'nullable|string|max:255',
            'motorcycle_models' => 'required|array|min:1',
            'motorcycle_models.*' => 'exists:motorcycle_models,id',
            'status' => 'required|in:Active,Inactive',
        ];

        // Add expiration fields validation for expirable products
        $expirableDescriptions = [
            'ENGINE OIL',
            'BRAKE FLUID (BRAKE OIL)',
            'GEAR OIL',
            'COOLANT / RADIATOR COOLANT',
            'CVT CLEANER',
            'TIRE SEALANT'
        ];

        if (in_array(strtoupper($this->product_description), $expirableDescriptions)) {
            $rules['manufacturing_date'] = 'nullable|date';
            $rules['batch_lot_number'] = 'nullable|string|max:255';
            $rules['expiration_date'] = 'nullable|date|after:manufacturing_date';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required.',
            'brand.required' => 'Brand is required.',
            'product_description.required' => 'Product description is required.',
            'product_description.exists' => 'Selected product description does not exist.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'SKU must be unique.',
            'motorcycle_models.required' => 'At least one motorcycle model must be selected.',
            'motorcycle_models.min' => 'At least one motorcycle model must be selected.',
            'motorcycle_models.*.exists' => 'Selected motorcycle model does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active or Inactive.',
            'batch_lot_number.required' => 'Batch/Lot number is required for this product type.',
            'expiration_date.required' => 'Expiration date is required for this product type.',
            'expiration_date.after' => 'Expiration date must be after manufacturing date.',
        ];
    }
}
