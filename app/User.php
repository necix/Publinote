<?php	namespace App;

use DB;
use Session;
use Cas;
use Exception;

class User 
{
	public static function connect()
	{
		Cas::authenticate();
		
		$user_id = strtr(Cas::user(), ['p' => 1,
									   'q' => 2,
									   'r' => 3,
									   's' => 4,
									   't' => 5,
									   'u' => 6,
									   'v' => 7,
									   'w' => 8,
									   'x' => 9]);
		$user_id = intval($user_id);
		
		
		if(DB::table('utilisateur')->where('utilisateur_ID', $user_id)->count() == 0)
			//si le numéro cas n'est pas connu alors on retourne faux
			return false;
		
		//dans le cas contraire on récupère les infos dans un bdd et on les stocke dans une variable de session	
		Session::put('user_id', $user_id);
		
		Session::put('first_name', 
						DB::table('utilisateur')->where('utilisateur_ID', $user_id)->pluck('utilisateur_Prenom') );
		Session::put('last_name', 
						DB::table('utilisateur')->where('utilisateur_ID', $user_id)->pluck('utilisateur_Nom') );
		
		//recherche du statut parmi les étudiants...
		if(DB::table('utilisateur__etudiant')->where('utilisateur__etudiant_RefUtilisateur', $user_id)->count() == 1)
			$user_status = 'student';
			
		//...sinon dans l'équipe
		else 
			{
				$status_id = DB::table('utilisateur__equipe')->where('utilisateur__equipe_RefUtilisateur', $user_id)->pluck('utilisateur__equipe_Role');
				$user_status = DB::table('ref__utilisateur__equipe_Role')->where('ref__utilisateur__equipe_Role_ID', $status_id)->pluck('ref__utilisateur__equipe_Role');
			}
			
		Session::put('status', $user_status );
		if(Session('status') == '')
			throw new Exception('Staut non identifié');
		
		return true;
	}
	
	public static function deconnect($route = '/')
	{
		if(self::isConnected())
		{
			Session::flush();
			DB::table('sessions')->where('id', Session::getId())->delete();
			Cas::logout(url($route));
		}
	}
	
	public static function isConnected()
	{
		if(Session::has('user_id'))
			return true;
	
		return false;
	}
	
	public static function id()
	{
		if(Session::has('user_id') && Session('user_id') != '')
			return Session('user_id');
	
		throw new Exception('User not connected');
	}
	
	public static function firstName()
	{
		if(Session::has('first_name') && Session('first_name') != '')
			return Session('first_name');
	
		throw new Exception('User not connected');
	}
	
	public static function lastName()
	{
		if(Session::has('last_name') && Session('last_name') != '')
			return Session('last_name');
	
		throw new Exception('User not connected');
	}
	public static function status()
	{
		if(Session::has('status') && Session('status') != '')
			return Session('status');
	
		throw new Exception('User not connected');
	}
	
	public static function parametersDefined($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
			
		if(self::status() != 'student')
			throw new Exception('Not a student');
		
		//vérification du profil étudiant
		if(DB::table('utilisateur__etudiant')->where('utilisateur__etudiant_RefUtilisateur', $user_id)->whereNull('utilisateur__etudiant_Profil')->count() != 0)
			return false;
		
		//vérification du statut primant/doublant/triplant
		else if(DB::table('utilisateur__etudiant')->where('utilisateur__etudiant_RefUtilisateur', $user_id)->whereNull('utilisateur__etudiant_Scolarite')->count() != 0)
			return false;
		
		else
			return true;
		
	}
}
