<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model {

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    /**
     * Attempt a user login.
     * @param array $post
     * @param string $loginString
     * @return int
     */
    public function login($post, $loginString) {
        $availability = DB::table("users")
                ->select("user_id")
                ->where("user_email", "=", $post['email'])
                ->where("user_password", "=", $post['password'])
                ->first();
        if ($availability) {
            if ($availability->user_id < 0) {
                return 0;
            } else {//If it is a valid login, store the string created to the table.
                $updateLogin = DB::table("users")
                        ->where("user_email", "=", $post['email'])
                        ->update(["user_login_string" => $loginString]);
                if ($updateLogin) {//If the update is succesful, return the user's ID.
                    return $availability->user_id;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

}
