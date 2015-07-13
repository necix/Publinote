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
							->orderBy('epreuve.date')
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
	
	public static function getGroupingsWithMark($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		//regroupements d'épreuves classées
		$groupings = DB::table('regroupement')
								->join('utilisateur_note_regroupement', 'regroupement.id', '=', 'utilisateur_note_regroupement.regroupement_id')
								->join('statistiques_regroupement', 'regroupement.id', '=', 'statistiques_regroupement.regroupement_id')
								->where('utilisateur_note_regroupement.utilisateur_id', $user_id)
								->select('regroupement.date as date_grouping', 
										 'regroupement.titre as title',
										 'utilisateur_note_regroupement.lu as read',
										 'utilisateur_note_regroupement.classement as rank',
										 'statistiques_regroupement.nb_participants as participants',
										 'regroupement.id as id')
								->get();
		return $groupings;
	}
	
	public static function getGroupingCategories($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
			
		$groupings = DB::table('regroupement_epreuve')
								->join('regroupement', 'regroupement_epreuve.regroupement_id', '=', 'regroupement.id')
								->join('epreuve', 'regroupement_epreuve.epreuve_id', '=', 'epreuve.id')
								->orderBy('epreuve.ue_id')
								->select('regroupement.id as id',
										 'epreuve.ue_id as ue_id')
								->get();
		
		foreach($groupings as $grouping)
			$categories[$grouping->id][] = DB::table('ue')->where('id', $grouping->ue_id)->pluck('sigle');
		
		return $categories;
	}
	
	public static function nbResultsNotRead($user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
			
		$nb_tests_not_read = DB::table('utilisateur_note_epreuve')->where('utilisateur_id', $user_id)->where('lu', false)->count();
		$nb_groupings_not_read = DB::table('utilisateur_note_regroupement')->where('utilisateur_id', $user_id)->where('lu', false)->count();
		
		return $nb_tests_not_read + $nb_groupings_not_read;
	}
	
	public static function getTestWithMark($test_id, $user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		//on le passe en mode lu
		DB::table('utilisateur_note_epreuve')
					->where('utilisateur_id', $user_id)
					->where('epreuve_id', $test_id)
					->update(['lu'=>true]);
		return DB::table('epreuve')
							->join('statistiques_epreuve', 'epreuve.id', '=', 'statistiques_epreuve.epreuve_id')
							->join('utilisateur_note_epreuve', 'epreuve.id', '=', 'utilisateur_note_epreuve.epreuve_id')
							->join('ue', 'epreuve.ue_id', '=', 'ue.id')
							->where('epreuve.id', $test_id)
							->where('utilisateur_note_epreuve.utilisateur_id', $user_id)
							->select('epreuve.titre as title',
									 'ue.sigle as category_sigle',
									 'ue.titre as category_title',
									 'utilisateur_note_epreuve.classement as rank',
									 'utilisateur_note_epreuve.note_reelle as mark_real',
									 'utilisateur_note_epreuve.note_ajustee as mark_ajusted',
									 'statistiques_epreuve.nb_participants as participants',
									 'ue.note_max as mark_max',
									 'epreuve.id as id')
							->first();
	}	

	public static function getTestWithoutMark($test_id, $user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		

		return DB::table('epreuve')
							->leftJoin('statistiques_epreuve', 'id', '=', 'statistiques_epreuve.epreuve_id')
							->join('ue', 'ue_id', '=', 'ue.id')
							->whereNull('statistiques_epreuve.min')
							->where('epreuve.visible', true)
							->where('epreuve.id', $test_id) 
							->select('epreuve.date as date_test', 
									 'epreuve.titre as title',
									 'ue.sigle as category_sigle',
									 'ue.titre as category_title',
									 'epreuve.id as id')
							->first();
	}	

	public static function getGroupingWithMark($grouping_id, $user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		DB::table('utilisateur_note_regroupement')
					->where('utilisateur_id', $user_id)
					->where('regroupement_id', $grouping_id)
					->update(['lu'=>true]);
					
		return DB::table('regroupement')
								->join('utilisateur_note_regroupement', 'regroupement.id', '=', 'utilisateur_note_regroupement.regroupement_id')
								->join('statistiques_regroupement', 'regroupement.id', '=', 'statistiques_regroupement.regroupement_id')
								->where('utilisateur_note_regroupement.utilisateur_id', $user_id)
								->where('regroupement.id', $grouping_id)
								->select('regroupement.date as date_grouping', 
										 'regroupement.titre as title',
										 'utilisateur_note_regroupement.lu as read',
										 'utilisateur_note_regroupement.classement as rank',
										 'statistiques_regroupement.nb_participants as participants',
										 'regroupement.id as id')
								->first();
	}	
	
	public static function getGrid($epreuve_id, $user_id = null)
	{
		if($user_id == null)
			$user_id = self::id();
		
		if(!Test::exists($epreuve_id))
			throw new Exception('Epreuve inexistante');
		
		if(!Test::isVisible($epreuve_id))
			throw new Exception('Epreuve inaccessible');	
	
		return DB::table('correction_qcm')->join('grille_qcm', function($join)
													{
														$join->on('correction_qcm.numero_qcm', '=', 'grille_qcm.numero_qcm')
															 ->on('correction_qcm.epreuve_id', '=', 'grille_qcm.epreuve_id');
															 })	
											->join('utilisateur_note_grille_qcm', function($join)
													{
														$join->on('correction_qcm.numero_qcm', '=', 'utilisateur_note_grille_qcm.numero_qcm')
															 ->on('correction_qcm.epreuve_id', '=', 'utilisateur_note_grille_qcm.epreuve_id');	 
													})
										  ->join('bareme', 'correction_qcm.bareme_id', '=', 'bareme.id')
										  ->where('grille_qcm.utilisateur_id', $user_id)
										 ->where('utilisateur_note_grille_qcm.utilisateur_id', $user_id)
										 ->where('correction_qcm.epreuve_id', $epreuve_id)
										  ->orderBy('correction_qcm.numero_qcm')
										  ->select('correction_qcm.numero_qcm as numero',
												   'correction_qcm.annule as annule',
												   'bareme.id as bareme_id',
												   'bareme.titre as bareme_titre',
												   'bareme.0_discordance as zero_discordance',
												   'bareme.1_discordance as one_discordance',
												   'bareme.2_discordance as two_discordance',
												   'bareme.3_discordance as three_discordance',
												   'bareme.4_discordance as four_discordance',
												   'bareme.5_discordance as five_discordance',
												   'utilisateur_note_grille_qcm.nb_discordances as nb_discordances',
												   'grille_qcm.item_a as grille_item_a',
												   'grille_qcm.item_b as grille_item_b',
												   'grille_qcm.item_c as grille_item_c',
												   'grille_qcm.item_d as grille_item_d',
												   'grille_qcm.item_e as grille_item_e',
												   'correction_qcm.item_a as correction_item_a',
												   'correction_qcm.item_b as correction_item_b',
												   'correction_qcm.item_c as correction_item_c',
												   'correction_qcm.item_d as correction_item_d',
												   'correction_qcm.item_e as correction_item_e')
										  ->get();
	}
	

}
