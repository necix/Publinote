<?php

use Illuminate\Database\Seeder;
use App\Test;

class EpreuveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nb_epreuves_classees_corrigees = 5;
		$nb_qcm_par_epreuve = 15;
		DB::table('epreuve')->truncate();
		DB::table('correction_qcm')->truncate();
		DB::table('statistiques_qcm')->truncate();
		DB::table('statistiques_epreuve')->truncate();
		DB::table('utilisateur_note_epreuve')->truncate();
		DB::table('grille_qcm')->truncate();
		DB::table('utilisateur_note_grille_qcm')->truncate();
		
		$session_scolaire_courrante_id = DB::table('session_scolaire')->whereNull('date_fin')->pluck('id');
		
		//affectation cyclique des UE aux épreuves
		$ue_disponibles = DB::table('ue')->where('session_scolaire_id', $session_scolaire_courrante_id);
		$nb_ue_disponibles = $ue_disponibles->count();
		$ue_affectee_id = null;
		
        
		//création de 5 épreuves classées et corrigées
		for($i = 1; $i <= $nb_epreuves_classees_corrigees; $i++)
		{
			//date de l'épreuve : on fait une épreuve par semaine
			$date_epreuve = time() - $i * 3600 * 24 * 7;
			
			//epreuve
			$ue_affectee_id = $ue_disponibles->offset($i % $nb_ue_disponibles)->pluck('id');
			$epreuve_id = DB::table('epreuve')->insertGetId(['session_scolaire_id' => $session_scolaire_courrante_id,
															 'ue_id' => $ue_affectee_id,
														     'titre' => 'Epreuve ' . $i,
														     'date'  => $date_epreuve,
														     'visible' => true, 
															 'date_modif_correction' => time() ]);
															 
			$array_correction_qcm_to_insert = null;
			$array_statistiques_qcm_to_insert = null;
			//15 QCM par epreuve
			for($j = 1; $j <= $nb_qcm_par_epreuve; $j++)
			{					
				//barème aléatoire parmi les choix possibles dans la table barème
				$nb_baremes = DB::table('bareme')->count();
				$bareme_id = DB::table('bareme')->offset(rand(0, $nb_baremes-1))->pluck('id');
				
				//réponse aux items
				$item_a = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé = mis en double
				$item_b = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_c = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_d = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_e = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				

				//correction QCM
				$array_correction_qcm_to_insert[] = ['epreuve_id' => $epreuve_id,
													 'numero_qcm' => $j,
													 'bareme_id'  => $bareme_id,
													 'annule' => (rand(1,50) == 1), //1 chance sur 50 d'annuler le QCM
													 'item_a' => $item_a,
													 'item_b' => $item_b,
													 'item_c' => $item_c,
													 'item_d' => $item_d,
													 'item_e' => $item_e,
													 'date_modif' => $date_epreuve + 3600*24 //corretion donnée 1 jour après l'épreuve
													 ];
				
				//statistiques aléatoires par qcm;
				$ecart = (rand(0,400)-200)/1000;
				$array_statistiques_qcm_to_insert[] = ['epreuve_id' => $epreuve_id,
													  'numero_qcm' => $j,
												 	  'taux_item_a' => pow(rand(0, 1000)/1000, 1/5),
												 	  'taux_item_b' => pow(rand(0, 1000)/1000, 1/5),
													  'taux_item_c' => pow(rand(0, 1000)/1000, 1/5),
													  'taux_item_d' => pow(rand(0, 1000)/1000, 1/5),
													  'taux_item_e' => pow(rand(0, 1000)/1000, 1/5),
													  'taux_0_discordance' => 0.20 + $ecart,
													  'taux_1_discordance' => 0.20 + $ecart * 0.5,
													  'taux_2_discordance' => 0.20 + $ecart * 0.1 ,
													  'taux_3_discordance' => 0.20 - $ecart * 0.1,
													  'taux_4_discordance' => 0.20 - $ecart * 0.5,
													  'taux_5_discordance' => 0.20 - $ecart,
													  ]; 
			}	
			DB::table('correction_qcm')->insert($array_correction_qcm_to_insert);
			DB::table('statistiques_qcm')->insert($array_statistiques_qcm_to_insert);
			
			
			//notes et classements individuels + grille QCM,
			$etudiants = DB::table('utilisateur')->where('statut','=','student')->get();
			$nb_etudiants = count($etudiants);
			$nb_qcm_epreuve = DB::table('correction_qcm')->where('epreuve_id', $epreuve_id)->count();
			
			//note max de l'ue
			$note_max_ue = DB::table('epreuve')->join('ue', 'epreuve.ue_id', '=', 'ue.id')
											   ->where('epreuve.id', $epreuve_id)
											   ->pluck('ue.note_max');
											   
			$array_utilisateur_note_epreuve_to_insert = null;
			$array_nb_discordances_to_insert = null;
			foreach($etudiants as $etudiant)
			{
				if( rand(1, 7) != 1 ) //1 chance sur 8 d'être absent à l'épreuve
				{
					$note_base = rand(0,1000)/1000;
					$note_reelle = $note_base*$note_max_ue;
					$note_ajustee = sqrt($note_base)*$note_max_ue;
					
					//note et classement
					$array_utilisateur_note_epreuve_to_insert[] = ['utilisateur_id' => $etudiant->id,
																   'epreuve_id' => $epreuve_id,
																   'classement' => rand(1, $nb_etudiants),
																   'note_reelle' => $note_reelle,
																   'note_ajustee' => $note_ajustee];
					
					//grille reponse
					for($k = 1; $k < $nb_qcm_epreuve + 1; $k++)
					{
						DB::table('grille_qcm')->insert(['utilisateur_id' => $etudiant->id,
														 'epreuve_id' => $epreuve_id,
														 'numero_qcm' => $k,
														 'item_a' => rand(0,1),
														 'item_b' => rand(0,1),
														 'item_c' => rand(0,1),
														 'item_d' => rand(0,1),
														 'item_e' => rand(0,1)]);
						
						//on compte les discordances
						$user_qcm = DB::table('grille_qcm')->where('utilisateur_id', $etudiant->id)
														   ->where('epreuve_id', $epreuve_id)
														   ->where('numero_qcm', $k)
														   ->first();
														   
						$correction_qcm = DB::table('correction_qcm')->where('epreuve_id', $epreuve_id)
																	 ->where('numero_qcm', $k)
																	 ->first();
																	 
						$nb_discordances = Test::countDiscordances($user_qcm, $correction_qcm);
						$array_nb_discordances_to_insert[] = ['utilisateur_id' => $etudiant->id,
															  'epreuve_id' => $epreuve_id,
															  'numero_qcm' => $k,
															  'nb_discordances' => $nb_discordances];
						
					}
				}	
			}
			
			DB::table('utilisateur_note_epreuve')->insert($array_utilisateur_note_epreuve_to_insert);
			DB::table('utilisateur_note_grille_qcm')->insert($array_nb_discordances_to_insert);
			
			//statistiques des épreuves
			$nb_participants = count(DB::table('utilisateur_note_epreuve')->where('epreuve_id', $epreuve_id)->get());
			$min = DB::table('utilisateur_note_epreuve')->where('epreuve_id', $epreuve_id)->min('note_reelle');
			$max = DB::table('utilisateur_note_epreuve')->where('epreuve_id', $epreuve_id)->max('note_reelle');
			$moy = DB::table('utilisateur_note_epreuve')->where('epreuve_id', $epreuve_id)->avg('note_reelle');
			 DB::table('statistiques_epreuve')->insert(['epreuve_id' => $epreuve_id,
													   'nb_participants' => $nb_participants,
													   'min' => $min,
													   'max' => $max,
													   'moy' => $moy,
													   'date_generation' => time()+10, //pour être sur que bien postérieur à date_modif_correction de épreuve
													  ]);
													   
		}
		

		
		
		
		//création de 1 épreuve corrigée sans classement
		for($i = 1; $i < 2; $i++)
		{
			//date de l'épreuve : on fait une épreuve par semaine
			$date_epreuve = time();
			
			//epreuve
			$ue_affectee_id = $ue_disponibles->offset($i % $nb_ue_disponibles)->pluck('id');
			$epreuve_id = DB::table('epreuve')->insertGetId(['session_scolaire_id' => $session_scolaire_courrante_id,
															 'ue_id' => $ue_affectee_id,
														     'titre' => 'Epreuve non classée ' . $i,
														     'date'  => $date_epreuve,
														     'visible' => true,
															 'date_modif_correction' => time()+10]);
							
			//15 QCM par epreuve
			for($j = 1; $j < 16; $j++)
			{					
				//barème aléatoire parmi les choix possibles dans la table barème
				$nb_baremes = DB::table('bareme')->count();
				$bareme_id = DB::table('bareme')->offset(rand(0, $nb_baremes-1))->pluck('id');
				
				//réponse aux items
				$item_a = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_b = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_c = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_d = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				$item_e = (rand(1,100) != 1)? rand(0,1) : 2; //1 chance sur 100 que l'item soit annulé
				

				//correction QCM
				DB::table('correction_qcm')->insert(['epreuve_id' => $epreuve_id,
													 'numero_qcm' => $j,
													 'bareme_id'  => $bareme_id,
													 'annule' => (rand(1,50) == 1), //1 chance sur 50 d'annuler le QCM
													 'item_a' => $item_a,
													 'item_b' => $item_b,
													 'item_c' => $item_c,
													 'item_d' => $item_d,
													 'item_e' => $item_e,
													 'date_modif' => $date_epreuve + 3600*24 //corretion donnée 1 jour après l'épreuve
													 ]);
			}	
			
		}
		
		//création de 1 épreuve non corrigée, invisible
		for($i = 1; $i < 2; $i++)
		{
			//date de l'épreuve : on fait une épreuve par semaine
			$date_epreuve = time();
			
			//epreuve
			$ue_affectee_id = $ue_disponibles->offset($i % $nb_ue_disponibles)->pluck('id');
			$epreuve_id = DB::table('epreuve')->insertGetId(['session_scolaire_id' => $session_scolaire_courrante_id,
															 'ue_id' => $ue_affectee_id,
														     'titre' => 'Epreuve non corrigée ' . $i,
														     'date'  => $date_epreuve,
														     'visible' => false ]);	
		}
		
    }
}
