<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CltLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('layup');
        $supplierId = $this->input('supplier_id');

        if ($id && ! $supplierId) {
            $layup = \App\Models\CltLayup::find($id);
            $supplierId = $layup ? $layup->supplier_id : null;
        }

        return [
            'supplier_id' => [
                $id ? 'sometimes' : 'required',
                Rule::exists('suppliers', 'id')->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id, $supplierId) {
                    $exists = \App\Models\CltLayup::where('supplier_id', $supplierId)
                        ->whereRaw('LOWER(name) = LOWER(?)', [$value])
                        ->where('id', '!=', $id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('The name has already been taken.');
                    }
                },
            ],
        ];
    }
}
