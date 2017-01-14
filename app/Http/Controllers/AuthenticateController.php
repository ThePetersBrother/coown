<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function index()
    {
        // Retrieve all the users in the database and return them
        $users = User::all();

        return $users;
    }

    public function authenticate(Request $request, LaravelFacebookSdk $fb)
    {
        $credentials = $request->only('facebook_access_token');

        Session::put('fb_user_access_token', (string)$credentials['facebook_access_token']);

        try {
            $response = $fb->get('/me?fields=id,name,email', $credentials['facebook_access_token']);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => 'something went wrong'], 401);
        }

        $facebook_user = $response->getGraphUser();
        $user = User::createOrUpdateGraphNode($facebook_user);

        Auth::login($user);
        $token = JWTAuth::fromUser($user);

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }
}