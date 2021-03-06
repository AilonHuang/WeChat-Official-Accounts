<?php

namespace App\Http\Controllers;

use App\Wechat\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store()
    {
        (new Tag)->store();
    }

    public function index()
    {
        (new Tag)->list();
    }

    public function update()
    {
        (new Tag)->update();
    }

    public function destroy()
    {
        (new Tag)->destroy();
    }

    public function batchUser()
    {
        (new Tag)->batchUser();
    }

    public function users()
    {
        (new Tag)->users();
    }

    public function userTags()
    {
        (new Tag)->userTags();
    }
}
