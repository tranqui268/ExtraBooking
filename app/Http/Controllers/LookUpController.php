<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LookUpController extends Controller
{
    public function index(){
        return view('lookup.index');
    }
}
