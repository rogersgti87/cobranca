<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use DB;

class HomeController extends Controller
{

    public function __construct()
    {

    }

    public function index(){

        return view('front.index');
    }

}
