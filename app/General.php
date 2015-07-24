<?php

namespace App;
use DB;
use Exception;

class General
{
    public static function profils()
	{
		return DB::table('profil_particulier_etudiant')
								->select('id', 'titre')
								->get();
	}
	
	public static function profilIdToTitle($profil_id)
	{
		if($profil_id == 0)
			return 'Classique';
		
		$profil = DB::table('profil_particulier_etudiant')->where('id', $profil_id)->first();
		if($profil)
			return $profil->titre;
		else
			throw new Exception('Profil inconnu');

	}
	
	public static function scolariteIdToTitle($scolarite_id)
	{
		switch($scolarite_id)
		{
			case 1 : return 'Primant';
			case 2 : return 'Doublant';
			case 3 : return 'Triplant';
			default : throw new Exception('Scolarité inconnue');
		}

	}
	
	public static function profilExists($profil_id)
	{
		if($profil_id == 0)
			return true;
			
		if($profil = DB::table('profil_particulier_etudiant')->where('id', $profil_id)->count() != 0)
			return true;
		else 
			return false;
	}
	
	public static function scolariteExists($scolarite_id)
	{
		switch($scolarite_id)
		{
			case 1 : ;
			case 2 : ;
			case 3 : return true;
			default : throw new Exception('Scolarité impossible');
		}
	}
	
	public static function currentSessionId()
	{
		return DB::table('session_scolaire')->whereNull('date_fin')
											->pluck('id');
	}
}
