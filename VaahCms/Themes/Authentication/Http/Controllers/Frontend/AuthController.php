<?php namespace VaahCms\Themes\Authentication\Http\Controllers\Frontend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Themes\Authentication\Models\AuthenticationUser;
use VaahCms\Themes\Authentication\Models\AuthenticationRegistration;


class AuthController extends Controller
{

    //----------------------------------------------------------
    public function login()
    {
      return redirect()->route('vh.frontend.authentication.signin');
    }
    //----------------------------------------------------------
    public function signOutUser()
    {
      \Auth::logout();
    }
    //----------------------------------------------------------
    public function signout(Request $request)
    {
        $this->signOutUser();
        return redirect('/');
    }
    //----------------------------------------------------------
    public function signin()
    {
      $this->signOutUser();
      return view('authentication::frontend.auth.signin');
    }
    //----------------------------------------------------------
    public function signinPost(Request $request)
    {

        if($request->type == 'password')
        {
            $response = AuthenticationUser::login($request);
        } else if($request->type == 'otp')
        {
            if(is_array($request->login_otp))
            {
                $inputs = [
                    'login_otp' => implode("", $request->login_otp)
                ];

                $request->merge($inputs);
            }

            $response = AuthenticationUser::loginViaOtp($request);
        }

        if((isset($response['status']) && $response['status'] == 'success')
          || (isset($response['success']) && $response['success'] == true))
        {
            $response['data']['redirect_url'] = url("/");
        }

        return response()->json($response);
    }
    //----------------------------------------------------------
    public function sendOtp(Request $request)
    {
        $response = AuthenticationUser::sendLoginOtp($request);

        return response()->json($response);
    }
    //----------------------------------------------------------
    public function sendResetCode(Request $request)
    {
        $response = AuthenticationUser::sendResetPasswordEmail($request);

        return response()->json($response);
    }
    //----------------------------------------------------------
    public function passwordResetAndSignin(Request $request)
    {
        $response = AuthenticationUser::passwordResetAndSignin($request);

        if($response['status'] == 'success')
        {
            $response['data']['redirect_url'] = url('/');
        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function signup()
    {
        $this->signOutUser();
        return view('authentication::frontend.auth.signup');
    }

    //---------------------------------------------------------
    public function signupPost(Request $request)
    {
        $response = AuthenticationRegistration::postCreate($request);
        return response()->json($response);
    }
    //---------------------------------------------------------
    public function activate(Request $request, $code)
    {
        $this->signOutUser();
        return view('authentication::frontend.auth.activate')
            ->with([ 'activation_code'=> $code ]);
    }
    //----------------------------------------------------------
    public function activatePost(Request $request, $code)
    {
        $request->merge(['activation_code' => $code]);
        $response = AuthenticationRegistration::activateRegistration($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------


}
