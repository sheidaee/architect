<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Cookie;


class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

    public function postLogin(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if ($this->auth->attempt($credentials, $request->has('remember'))) {
//            return redirect()->intended($this->redirectPath());
//            return response(array('msg' => 'Login Successfull'), 200) // 200 Status Code: Standard response for successful HTTP request
//            ->header('Content-Type', 'application/json');
            $response = Response($credentials, 200);
//            setcookie('prUser', serialize($credentials), time()+3600, '/', null, false, false);
//            $response->headers->setCookie(Cookie('prUser', $credentials, 0, null, null, false, false));
//            setCookie(Cookie('prUser', $credentials, 0, null, null, false, false));
            return Response($credentials, 200)->withCookie(Cookie('prUser', $credentials, 0, null, null, false, false));

        }

        $error['*'] = trans('validation.validationError');
        return new Response(json_encode($error), 400);

    }

    public function getLogout()
    {
        $this->auth->logout();
        setcookie('prUser', null, time()-3600, '/', null, false, false);
        return response(null, 200);
            //->withCookie(cookie('prUser', null, -200));
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if ( ! is_null($this->user))
        {
            $this->refreshRememberToken($user);
        }

        if (isset($this->events))
        {
            $this->events->fire('auth.logout', [$user]);
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

//        setcookie('prUser', null, time()-3600, '/', null, false, false);

//        $this->loggedOut = true;
        return response(null, 200);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(AuthRequest $request)
    {
//        return new Response($request->has('password_confirmation'), 200);

//        $validator = $this->registrar->validator($request->all());

//        if ($validator->fails())
//        {
//            $this->throwValidationException(
//                $request, $validator
//            );
//        }

        $this->auth->login($this->registrar->create($request->all()));

//        return redirect($this->redirectPath());
        setcookie('prUser', serialize($this->auth->user()), time()+3600, '/', null, false, false);
        return $this->auth->user();
    }
}
