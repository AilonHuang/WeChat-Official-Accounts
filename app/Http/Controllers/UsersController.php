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
}
