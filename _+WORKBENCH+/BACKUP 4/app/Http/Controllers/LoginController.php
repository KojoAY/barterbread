<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

//Auth::routes();


class LoginController extends Controller
{
    //
    public function showLoginForm() {

        $pageURL = $_SERVER['REQUEST_URI'];
        $hitDateTime = strtotime("now");
        $viewerIP = $_SERVER['REMOTE_ADDR'];

        DB::table("bb_visitor_count")
        ->insert(
            [
                "pageurl" => $pageURL,
                "visitorip" => $viewerIP,
                "datetime" => $hitDateTime
            ]
        );
        
    	// check if Session['traderToken'] is set
        if (Session::has("traderToken")) {
            // if Session['traderToken'] is set, go to home page
            $rdirUrlIsset = @$_GET['rdirURL'];
            $rdirUrl = htmlspecialchars($rdirUrlIsset);

            // redirect to profile page
            if(!session()->has('url.intended')) {
                if(!empty($rdirUrl)){
                    session(['url.intended' => url("/" . $rdirUrl)]);
                } else {
                    session(['url.intended' => url("/trader/profile")]);
                }
            }
            return redirect()->intended();
        } else {
            // if Session['traderToken'] is not set go to login page
            return view("auth.login");
        }
    }

    public function login(Request $request) {

        $pageURL = $_SERVER['REQUEST_URI'];
        $hitDateTime = strtotime("now");
        $viewerIP = $_SERVER['REMOTE_ADDR'];

        DB::table("bb_visitor_count")
        ->insert(
            [
                "pageurl" => $pageURL,
                "visitorip" => $viewerIP,
                "datetime" => $hitDateTime
            ]
        );
        
        
        // 
        $email = $request->get('email');
        $password = $request->get('password');
        $rdirUrl = htmlspecialchars($request->get('rdirURL'));
        //$hashPass = Hash::make($password);

        // increase error counter to by 1 each time there is an error
        $errorCount =  $request->get('errorCnt');
        $errorCount += 1;

    	// check records if user exists
        $checkUserExists = DB::table('bb_account')
                ->select('*')
                ->where('email', '=', $email)->first();

        // if trader's email address exists and password matches
        if (($checkUserExists !== null) && (Hash::check($password, $checkUserExists->hash_pass))) {
            // set the following Sessions
            Session::put("traderToken", $checkUserExists->usercode);
            Session::put("traderFName", $checkUserExists->fname);
            Session::put("traderLName", $checkUserExists->lname);

            if(!session()->has('url.intended')) {
                session(['url.intended' => url("/" . $rdirUrl)]);
            }
            return redirect()->intended();
           
        } else {
            // check if a login error has been committed
            // if it's been committed less than 1 to 3 times
            if ($errorCount < 3) {
                // error message (Wrong login details)
                $errorMessage = "<label style=\"color: #c81111; text-align:center; margin-bottom:10px;\">
                            Email address/password does not exit.
                        </label>";

                // assign error message
                $request->session()->flash('errorMessage', $errorMessage);
                // return to login page to login again
                return redirect()->action('LoginController@showLoginForm', ['_err'=>$errorCount]); 
            } else {
                // redirect to the register page
                return redirect()->action('RegisterController@showRegistrationForm'); 
            }
        }
    }

    public function logout(){

        $pageURL = $_SERVER['REQUEST_URI'];
        $hitDateTime = strtotime("now");
        $viewerIP = $_SERVER['REMOTE_ADDR'];

        DB::table("bb_visitor_count")
        ->insert(
            [
                "pageurl" => $pageURL,
                "visitorip" => $viewerIP,
                "datetime" => $hitDateTime
            ]
        );
        
        
    	// 
		Session::flush();
		if (!Session::has('traderToken')) {
			return redirect()->action('HomeController@index');
		} else {}
	}
}