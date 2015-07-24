<?php	namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Exception;

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
		
        else if (User::status() == 'tutor')
			return redirect('/espace_tuteur');
		
		else if (User::status() == 'admin')
			return redirect('/espace_admin');
		else
			throw new Exception('Profil non reconnu');
	}
}
