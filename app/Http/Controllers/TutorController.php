<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TutorController extends Controller
{
	function __construct()
	{
		$this->middleware('tutor');
	}
	
    public function index()
    {
        
    }
}
