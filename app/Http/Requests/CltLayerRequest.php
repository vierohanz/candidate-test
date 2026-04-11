<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CltLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('layer');
        $layupId = $this->input('layup_id');

        if ($id && ! $layupId) {
            $layer = \App\Models\CltLayer::find($id);
            $layupId = $layer ? $layer->layup_id : null;
        }

        return [
            'layup_id' => [
                $id ? 'sometimes' : 'required',
                Rule::exists('clt_layups', 'id')->whereNull('deleted_at'),
            ],
            'layer_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($id, $layupId) {
                    $exists = \App\Models\CltLayer::where('layup_id', $layupId)
                        ->where('layer_order', $value)
                        ->where('id', '!=', $id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('The layer order has already been taken for this layup.');
                    }
                },
            ],
            'thickness' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'angle' => 'required|numeric',
        ];
    }
}
