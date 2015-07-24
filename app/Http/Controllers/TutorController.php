<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Test;
use App\User;

class TutorController extends Controller
{
	function __construct()
	{
		$this->middleware('tutor');
	}
	
    public function index()
    {
		return view('tuteur.tableau_de_bord')->with(['first_name' => User::firstName(),
													 'last_name' => User::lastName(),
													 'tests' => Test::getAllTests()]);
    }
}
