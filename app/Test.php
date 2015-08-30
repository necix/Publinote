<?php

namespace App;

use DB;
use Exception;

class Test 
{
    public static function exists($id)
	{
		if(DB::table('epreuve')->where('id', $id)->count() != 0 )
			return true;
			
		return false;
	}
	
	public static function isVisible($id)
	{
		if(!self::exists($id))
			throw new Exception('Test does not exist');
		
		return DB::table('epreuve')->where('id', $id)->pluck('visible');
	}
	
	public static function isCorrected($id)
	{
		if(!self::exists($id))
			throw new Exception('Test does not exist');
			
		if(DB::table('statistiques_epreuve')->where('epreuve_id', $id)->count() != 0)
			return true;
		
		return false;
	}
	
	public static function countDiscordances($user_qcm, $correction_qcm)
	{
		if($correction_qcm->annule == true)
			return -1;//throw new Exception('Item annulé');
		
		$nb_disc = 0;
			
		
		if($correction_qcm->item_a != 2 and $correction_qcm->item_a != $user_qcm->item_a) $nb_disc++;
		if($correction_qcm->item_b != 2 and $correction_qcm->item_b != $user_qcm->item_b) $nb_disc++;
		if($correction_qcm->item_c != 2 and $correction_qcm->item_c != $user_qcm->item_c) $nb_disc++;
		if($correction_qcm->item_d != 2 and $correction_qcm->item_d != $user_qcm->item_d) $nb_disc++;
		if($correction_qcm->item_e != 2 and $correction_qcm->item_e != $user_qcm->item_e) $nb_disc++;
			
		return $nb_disc;
	
	}
	
	public static function compactQCM($qcm)
	{
		$compact = '';
		switch($qcm[0])
		{
			case 1 : $compact .= 'A'; break;
			case 2 : $compact .= '(A)'; break;
		}
		switch($qcm[1])
		{
			case 1 : $compact .= 'B'; break;
			case 2 : $compact .= '(B)'; break;
		}
		switch($qcm[2])
		{
			case 1 : $compact .= 'C'; break;
			case 2 : $compact .= '(C)'; break;
		}
		switch($qcm[3])
		{
			case 1 : $compact .= 'D'; break;
			case 2 : $compact .= '(D)'; break;
		}
		switch($qcm[4])
		{
			case 1 : $compact .= 'E'; break;
			case 2 : $compact .= '(E)'; break;
		}
		
		if($compact == '')
			$compact = 'Rien';

		
		return $compact;
	}
	
	public static function discordanceToNote($qcm)
	{
		switch($qcm->nb_discordances)
		{
			case 0 : return $qcm->zero_discordance; 
			case 1 : return $qcm->one_discordance; 
			case 2 : return $qcm->two_discordance; 
			case 3 : return $qcm->three_discordance; 
			case 4 : return $qcm->four_discordance; 
			case 5 : return $qcm->five_discordance;
			default : throw new Exception('nombre de discordances inconnu');
		}
	}
	
	public static function getAllTests()
	{
		return DB::table('epreuve')->where('epreuve.session_scolaire_id', General::currentSessionId())
								   ->join('ue', 'epreuve.ue_id', '=', 'ue.id')
								   ->orderBy('epreuve.date')
								   ->select('epreuve.id as id',
											'epreuve.titre as titre',
											'epreuve.date as date',
											'epreuve.visible as visible',
											'ue.sigle as ue')
								   ->get();
	}
	
	public static function getTest($test_id)
	{
		return DB::table('epreuve')->where('epreuve.id', $test_id)
								   ->join('ue', 'epreuve.ue_id', '=', 'ue.id')
								   ->join('statistiques_epreuve', 'epreuve.id', '=', 'statistiques_epreuve.epreuve_id', 'left outer')
								   ->select('epreuve.id as id',
											'epreuve.titre as titre',
											'epreuve.date as date',
											'epreuve.visible as visible',
											'ue.sigle as ue',
											'statistiques_epreuve.nb_participants as nb_participants',
											'statistiques_epreuve.min as note_min',
											'statistiques_epreuve.moy as note_moy',
											'statistiques_epreuve.max as note_max',
											'statistiques_epreuve.repartition_notes as repartition_notes'
											)
								   ->first();
	}
	
	public static function getQCMGrids($test_id)
	{
				
		return DB::table('correction_qcm')->join('statistiques_qcm', function($join)
											{	
												$join->on('correction_qcm.numero_qcm', '=', 'statistiques_qcm.numero_qcm')
													 ->on('correction_qcm.epreuve_id', '=', 'statistiques_qcm.epreuve_id');}, null, null,  'left outer'
											)	
										 ->join('bareme', 'correction_qcm.bareme_id', '=', 'bareme.id')
										 ->where('correction_qcm.epreuve_id', $test_id)
										 ->orderBy('numero')
										 ->select('correction_qcm.numero_qcm as numero',
												   'correction_qcm.annule as annule',
												   'bareme.id as bareme_id',
												   'bareme.titre as bareme_titre',
												   'correction_qcm.item_a as correction_item_a',
												   'correction_qcm.item_b as correction_item_b',
												   'correction_qcm.item_c as correction_item_c',
												   'correction_qcm.item_d as correction_item_d',
												   'correction_qcm.item_e as correction_item_e',
												   'statistiques_qcm.taux_item_a as taux_reussite_item_a',
												   'statistiques_qcm.taux_item_b as taux_reussite_item_b',
												   'statistiques_qcm.taux_item_c as taux_reussite_item_c',
												   'statistiques_qcm.taux_item_d as taux_reussite_item_d',
												   'statistiques_qcm.taux_item_e as taux_reussite_item_e',
												   'statistiques_qcm.taux_0_discordance as taux_0_discordance',
												   'statistiques_qcm.taux_1_discordance as taux_1_discordance',
												   'statistiques_qcm.taux_2_discordance as taux_2_discordance',
												   'statistiques_qcm.taux_3_discordance as taux_3_discordance',
												   'statistiques_qcm.taux_4_discordance as taux_4_discordance',
												   'statistiques_qcm.taux_5_discordance as taux_5_discordance')
										 ->get();
												   
	}
	
	public static function nbGrids($test_id)
	{
		return count(DB::table('grille_qcm')->where('epreuve_id', $test_id)
									  ->select('utilisateur_id')
									  ->distinct()
									  ->get()//compte le nombre de QCM par epreuve, 
									 );
	}
	
	public static function nbQCMS($test_id)
	{
		return DB::table('correction_QCM')->where('epreuve_id', $test_id)
										  ->count();
	}
	
	public static function getTutors($epreuve_id)
	{
		//récupération de l'id de l'ue de l'épreuve
		$ue_id = DB::table('epreuve')->where('id', $epreuve_id)
									->pluck('ue_id');
		
		//récupération des utilisateurs associés à l'ue
		return DB::table('utilisateur_ue')->join('utilisateur', 'utilisateur_ue.utilisateur_id', '=', 'utilisateur.id')
										  ->where('utilisateur_ue.ue_id', $ue_id)
										  ->select('utilisateur.nom as last_name',
												   'utilisateur.prenom as first_name')
										  ->get();							  
	}
	
	public static function isTutorTest($tutor_id, $test_id)
	{
		if(!self::exists($test_id))
			throw new Exception('Test does not exists');
		
		if(User::status($tutor_id) != 'tutor')
			throw new Exception('Not a tutor');
		
		//récup de l'id de l'ue de l'épreuve
		$ue_id = DB::table('epreuve')->where('id', $test_id)
									->pluck('ue_id');
		$nb_match = DB::table('utilisateur_ue')->where('utilisateur_id', $tutor_id)
											   ->where('ue_id', $ue_id)
											   ->count();
		if($nb_match == 0)
			return false;
		else 
			return true;
	}
	
	public static function getCorrection($test_id)
	{
		if(!self::exists($test_id))
			throw new Exception('Test does not exists');
			
		return DB::table('correction_qcm') ->join('bareme', 'correction_qcm.bareme_id', '=', 'bareme.id')
											->where('correction_qcm.epreuve_id', $test_id)
											->orderBy('numero')
											->select('correction_qcm.numero_qcm as numero',
												     'correction_qcm.annule as annule',
												     'bareme.id as bareme_id',
												     'bareme.titre as bareme_titre',
													 'correction_qcm.epreuve_id as epreuve_id',
											  	     'correction_qcm.item_a as correction_item_a',
												     'correction_qcm.item_b as correction_item_b',
												     'correction_qcm.item_c as correction_item_c',
												     'correction_qcm.item_d as correction_item_d',
												     'correction_qcm.item_e as correction_item_e')
											->get();
		
	}
	
	public static function isQCMCorrected($epreuve_id, $numero_qcm)
	{
		if(!self::exists($epreuve_id))
			throw new Exception('Test does not exists');
		
		if( 0 == DB::table('correction_qcm')->where('epreuve_id', $epreuve_id)
											->where('numero_qcm', $numero_qcm)
											->count())
			return false;
			
		return true;
	}
	
	public static function setQCMCorrection($epreuve_id, $numero_qcm, $bareme_id, $annule, $item_a, $item_b, $item_c, $item_d, $item_e)
	{
		//si la correction existe, on fait un update
		if(self::isQCMCorrected($epreuve_id, $numero_qcm))
			DB::table("correction_qcm")->where('epreuve_id', $epreuve_id)
									   ->where('numero_qcm', $numero_qcm)
									   ->update(['bareme_id' => $bareme_id,
												'annule' => $annule,
												'item_a' => $item_a,
												'item_b' => $item_b,
												'item_c' => $item_c,
												'item_d' => $item_d,
												'item_e' => $item_e,
												'date_modif' => time()]);
		else //sinon un insert
			DB::table("correction_qcm")->insert(['epreuve_id' => $epreuve_id,
												'numero_qcm' => $numero_qcm,
												'bareme_id' => $bareme_id,
												'annule' => $annule,
												'item_a' => $item_a,
												'item_b' => $item_b,
												'item_c' => $item_c,
												'item_d' => $item_d,
												'item_e' => $item_e,
												'date_modif' => time()]);
		self::updateModificationDate($epreuve_id);
	}
	
	public static function deleteQCMCorrection($epreuve_id, $numero_qcm)
	{
		DB::table("correction_qcm")->where('epreuve_id', $epreuve_id)
								   ->where('numero_qcm', $numero_qcm)
								   ->delete();
		self::updateModificationDate($epreuve_id);
	}
	
	public static function updateModificationDate($epreuve_id)
	
	{
		DB::table('epreuve')->where('id', $epreuve_id)
							->update(['date_modif_correction' => time()]); 
	}
	
	public static function isRankingObsolete($epreuve_id)
	{
		//verifie qu'il y a un classement, si non retourne false
		if(!self::isCorrected($epreuve_id))
			return false;
			
		//recupère date_modif_correction
		$date_modif_correction = DB::table('epreuve')->where('id', $epreuve_id)
													 ->pluck('date_modif_correction');
		//recupère date_generation
		$date_generation_statistiques = DB::table('statistiques_epreuve')->where('epreuve_id', $epreuve_id)
																  ->pluck('date_generation');
		
		return $date_modif_correction > $date_generation_statistiques;
	}
}
