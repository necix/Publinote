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
		
		//remplacement de la première lettre par un chiffre p******* vers 1*******
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
		
		
		if(DB::table('utilisateur')->where('id', $user_id)->count() == 0)
			//si le numéro cas n'est pas connu alors on retourne faux
			return false;
		
		//dans le cas contraire on récupère les infos dans un bdd et on les stocke dans une variable de session	
		Session::put('user_id', $user_id);
		
		Session::put('first_name', 
						DB::table('utilisateur')->where('id', $user_id)->pluck('prenom') );
		Session::put('last_name', 
						DB::table('utilisateur')->where('id', $user_id)->pluck('nom') );
		
		//recherche du statut de l'utilisateur
		$user_status = DB::table('utilisateur')->where('id', $user_id)->pluck('statut');
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
		//if(DB::table('utilisateur')->where('id', $user_id)->pluck('profil_particulier_etudiant') == null)
			//return false;
		
		//vérification du statut primant/doublant/triplant
		else if(DB::table('utilisateur')->where('id', $user_id)->pluck('scolarite') == null)
			return false;
		
		else
			return true;
		
	}
	
	public static function getTestsWithMark($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		//épreuves corrigées
		$tests = DB::table('epreuve')
							->join('utilisateur_note_epreuve', 'id', '=', 'utilisateur_note_epreuve.epreuve_id')
							->join('statistiques_epreuve', 'id', '=', 'statistiques_epreuve.epreuve_id')
							->join('ue', 'ue_id', '=', 'ue.id')
							->join('session_scolaire', 'epreuve.session_scolaire_id', '=', 'session_scolaire.id')
							->whereNull('session_scolaire.date_fin')
							->where('utilisateur_note_epreuve.utilisateur_id', $user_id)
							->where('epreuve.visible', true)
							->select('epreuve.date as date_test', 
									 'epreuve.titre as title',
									 'ue.sigle as category',
									 'utilisateur_note_epreuve.classement as rank',
									 'statistiques_epreuve.nb_participants as participants',
									 'utilisateur_note_epreuve.lu as read',
									 'epreuve.id as id')
							->get();
							
		return $tests;
	}
	
	public static function getTestsWithoutMark($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		//épreuves non classées
		$tests = DB::table('epreuve')
							->leftJoin('statistiques_epreuve', 'id', '=', 'statistiques_epreuve.epreuve_id')
							->join('ue', 'ue_id', '=', 'ue.id')
							->whereNull('statistiques_epreuve.min')
							->where('epreuve.visible', true)
							->select('epreuve.date as date_test', 
									 'epreuve.titre as title',
									 'ue.sigle as category',
									 'epreuve.id as id')
							->get();
		return $tests;
	}
	
	
	public static function nbResultsNotRead($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
			
		$nb_tests_not_read = DB::table('utilisateur_note_epreuve')->where('utilisateur_id', $user_id)->where('lu', false)->count();
		$nb_groupings_not_read = DB::table('utilisateur_note_regroupement')->where('utilisateur_id', $user_id)->where('lu', false)->count();
		
		return $nb_tests_not_read + $nb_groupings_not_read;
	}
}
