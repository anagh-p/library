<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class UserController extends Controller {

    var $user;

    public function __construct() {
        $this->user = new User();
    }

    /**
     * Attempt to login a user entering the credentials.
     * @param Request $request
     * @return Array
     */
    public function login(Request $request) {

        $params = $request->all();
        $loginArray = [];
        $loginString = Str::random(60);
        
        $login = $this->user->login($params, $loginString);
        $loginArray['userId'] = $login;
        if ($login > 0) {
            $loginArray['randomId'] = $loginString;
        } else {
            $loginArray['randomId'] = '';
        }

        return $loginArray;
    }

}
