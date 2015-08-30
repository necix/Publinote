<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddCorrectionRequest;
use App\Http\Controllers\Controller;
use App\Test;
use App\User;
use App\General;
use Session;

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
											 'isTutorTest' => Test::isTutorTest(User::id(), $epreuve_id),
											 'obsolete' => Test::isRankingObsolete($epreuve_id)]);
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
														 'baremes' => General::getBaremes(),
														 'epreuve_id' => $epreuve_id,
														 'obsolete' => Test::isRankingObsolete($epreuve_id)]);
	}
	
	public function add_qcm(AddCorrectionRequest $r)
	{
		//verification unicité
		if(Test::isQCMCorrected($r->input('epreuve_id'), $r->input('numero_qcm')))
		{
			Session::flash('flash_message_qcm', 'Question '.$r->input('numero_qcm').' déjà existante');
			return redirect()->back();
		}
		
		//insertion dans bdd
		Test::setQCMCorrection($r->input('epreuve_id'),
							   $r->input('numero_qcm'),
							   $r->input('bareme_id'),
							   filter_var($r->input('annule'), FILTER_VALIDATE_BOOLEAN),
							   $r->input('item_a'),
							   $r->input('item_b'),
							   $r->input('item_c'),
							   $r->input('item_d'),
							   $r->input('item_e'));
		//redirection vers la page d'édition
		
		Session::flash('flash_message_qcm', 'Question '.$r->input('numero_qcm').' ajoutée');
		return redirect()->back();
	}
	
	public function delete_qcm(Request $r)
	{
		Test::deleteQCMCorrection($r->input('epreuve_id'), $r->input('numero_qcm'));
		
		Session::flash('flash_message_qcm', 'Question '.$r->input('numero_qcm').' supprimée');
		return redirect()->back();
	}
}
