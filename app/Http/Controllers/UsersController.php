<?php

namespace App\Http\Controllers;

use App\Wechat\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        (new User)->list();
    }

    public function show()
    {
        (new User)->info();
    }

    public function batchInfo()
    {
        (new User)->batchInfo();
    }

    public function syncUsers()
    {
        (new User)->syncUsers();
    }

    public function updateUserInfo()
    {
        (new User)->updateUserInfo();
    }

    public function userInfo(Request $request)
    {
        $user = new User;
        if (!$request->code){
            $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $jumpurl = $user->oauth2_authorize($redirect_url, "snsapi_userinfo", "123");
            Header("Location: $jumpurl");
            exit();
        }else{
            $access_token = $user->oauth2_access_token($request->code);
            $openid = $access_token['openid'];
            $userinfo = $user->info($openid);
            $sexes = array("", "男", "女");
            return view('users.info', compact('userinfo', 'sexes'));
        }
    }
}
