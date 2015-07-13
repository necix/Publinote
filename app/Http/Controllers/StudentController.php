<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Test;
use App\Http\Requests;
use App\Http\Requests\PanelRequest;
use App\Http\Controllers\Controller;
use Session;

class StudentController extends Controller
{
   	function __construct()
	{
		$this->middleware('student');
	}
	
    public function index()
    {
		//Session::put('flash_message',  'ceci est un message');
		//return print_r(User::getTests(), true);
		
		return view('etudiant.mes_resultats')
			->with([ 'nb_results_not_read' => User::nbResultsNotRead(),
					'first_name' => User::firstName(),
					'last_name' => User::lastName(),
					'home_message' => '',
					'tests_with_mark' => User::getTestsWithMark(),
					'tests_without_mark' => User::getTestsWithoutMark(),
					'groupings' => User::getGroupingsWithMark(),
					'grouping_categories' => User::getGroupingCategories(),
			]);
			
    }
	
	public function getParams()
	{
		return view('etudiant.parametres');
	}
	
	public function postParams(StudentParamsRequest $r)
	{
		return 'e';
	}
	
	public function testPanel(PanelRequest $r)
	{
		if(!$r->ajax())
			abort(403);
	
		else if($r->test_grouping_type == 'test')	
				return view('etudiant.ajax.volet_epreuve')
										->withTest(User::getTestWithMark($r->test_grouping_id));

		else if($r->test_grouping_type == 'test_pending')	
				return view('etudiant.ajax.volet_epreuve_en_attente')
										->withTest(User::getTestWithoutMark($r->test_grouping_id));

		else if($r->test_grouping_type == 'grouping')	
				return view('etudiant.ajax.volet_groupement')
										->withTest(User::getGroupingWithMark($r->test_grouping_id));										
	}
	
	public function show($id)
	{
		//controle que l'épreuve existe
		if(!Test::exists($id))
			return redirect('/');
			
		//contrôle que l'épreuve est visible
		if(!Test::isVisible($id))
			return redirect('/');
			
		//demande si l'épreuve est corrigée
		$is_corrected = Test::isCorrected($id);
		
		//appelle la vue
		return view('etudiant.epreuve')
				->with(['nb_results_not_read' => User::nbResultsNotRead(),
						'first_name' => User::firstName(),
						'last_name' => User::lastName(),
						'test' => ($is_corrected)? User::getTestWithMark($id) : User::getTestWithoutMark($id),
						'is_corrected' => $is_corrected,
						'qcms' => User::getGrid($id) ]);
	}
}
