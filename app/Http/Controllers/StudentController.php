<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
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
			]);
			
        // return view('etudiant.mes_resultats')
					// ->with([ 'nb_results' => 5,
							// 'first_name' => User::firstName(),
							// 'last_name' => User::lastName(),
							// 'home_message' => '',
							// 'tests' => [[ 'date_creation' => '4/10/2015', 'title' => 'Épreuve 1', 'category' => 'UE1', 'status' => 'en attente', 'rank' => '305', 'participants' => '1852', 'read' => true, 'id' => 17 ]
										// ],
					// ]);
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
		return view('etudiant.ajax.volet_epreuve');
	}
}
