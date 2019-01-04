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
}
