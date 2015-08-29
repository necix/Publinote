<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddCorrectionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		//si statut différent de tuteur => impossible
		
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ["numero_qcm" => "required|numeric|min:1|max:100",
					
            //
        ];
    }
}
