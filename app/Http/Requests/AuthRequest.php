<?php namespace App\Http\Requests;


use App\Http\Requests\Request;
use Input;

class AuthRequest extends Request {

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
//        if (url() == 'project.dev/auth/register')
//        if ($this->method('POST'))
        if($this->fullUrl() == 'http://project.ir/auth/register')
        {
            return [
                'name'                  => 'required | min: 3',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required | confirmed',
                'password_confirmation' => 'required '
            ];
        } else
        {
            return [
                'email'    => 'required|email',
                'password' => 'required'
            ];
        }
    }

}
