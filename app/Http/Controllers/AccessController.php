<?php	namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;

class AccessController extends Controller
{
	function __construct()
	{
		$this->middleware('sessioncas');
	}
	
    public function index()
    {
		if (User::status() == 'student')
			return redirect('/mes_resultats');
		
        else if (User::status() == 'tuteur')
			return redirect('/espace_tuteur');
		
		else if (User::status() == 'admin')
			return redirect('/espace_admin');
	}
}
