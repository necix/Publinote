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
								   ->select('epreuve.id as id',
											'epreuve.titre as titre',
											'epreuve.date as date',
											'epreuve.visible as visible',
											'ue.sigle as ue')
								   ->get();
	}
	
	public static function nbGrids($test_id)
	{
		return DB::table('grille_qcm')->where('epreuve_id', $test_id) //compte le nombre d'items, 5 items par qcm donc on divise par 5
									  ->count() / 5;
	}
	
	public static function nbQCMS($test_id)
	{
		return DB::table('correction_QCM')->where('epreuve_id', $test_id)
										  ->count();
	}
}
