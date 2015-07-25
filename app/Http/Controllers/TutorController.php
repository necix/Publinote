<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Test;
use App\User;
use App\General;

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
	
	public function show($epreuve_id)
	{
		//verifie que l'épreuve existe
		if(!Test::exists($epreuve_id))
			return redirect('/');
		
		return view('tuteur.epreuve')->with(['first_name' => User::firstName(),
											 'last_name' => User::lastName(),
											 'test' => Test::getTest($epreuve_id),
											 'tutors' => Test::getTutors($epreuve_id),
											 'qcmGrid' => Test::getQCMGrids($epreuve_id),
											 'nbQCMs' => Test::nbQCMs($epreuve_id),
											 'nbGrids' => Test::nbGrids($epreuve_id),
											 'isTutorTest' => Test::isTutorTest(User::id(), $epreuve_id)]);
	}
	
	public function edit($epreuve_id)
	{
		if(!Test::exists($epreuve_id))
			return redirect('/');
		
		if(!Test::isTutorTest(User::id(), $epreuve_id))
			return redirect('/');
			
		return view('tuteur.modifier_correction')->with(['first_name' => User::firstName(),
														 'last_name' => User::lastName(),
														 'corrections' => Test::getCorrection($epreuve_id),
														 'baremes' => General::getBaremes()]);
	}
	
	public function deleteQCM(Request $r)
	{
		if(!$r->ajax())
			abort(403);
		
		return 'OK';
	}
}
