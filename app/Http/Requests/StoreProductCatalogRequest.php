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
            'warehouse' => 'required|string|max:255',
            'is_general' => 'nullable|boolean',
            'motorcycle_models' => $this->boolean('is_general') ? 'nullable|array' : 'required|array|min:1',
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
            'warehouse.required' => 'Warehouse is required.',
            'product_description.required' => 'Product category is required.',
            'product_description.exists' => 'Selected product category does not exist.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'SKU must be unique.',
            'motorcycle_models.required' => 'Please select at least one compatible motorcycle model or check General Item.',
            'motorcycle_models.min' => 'Please select at least one compatible motorcycle model or check General Item.',
            'motorcycle_models.*.exists' => 'Selected motorcycle model does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active or Inactive.',
            'batch_lot_number.required' => 'Batch/Lot number is required for this product type.',
            'expiration_date.required' => 'Expiration date is required for this product type.',
            'expiration_date.after' => 'Expiration date must be after manufacturing date.',
        ];
    }
}
