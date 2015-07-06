<?php	namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;

class SessionCASController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	public function connect()
	{
	
		if(User::connect())
			return redirect('/');
		else
			return redirect('/inconnu');
	}

	public function deconnect()
	{
		User::deconnect('/deconnexion_reussie');
	}
}
