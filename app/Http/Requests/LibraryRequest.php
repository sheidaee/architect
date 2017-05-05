<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\Response;

class LibraryRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
        if(url() == 'project.dev/libraries')
            return [
                'name' => 'required|unique:libraries,name',
                'path' => 'required|unique:libraries'
            ];
        else
            return [
                'name' => 'required',
                'path' => 'required'
            ];
	}

}
