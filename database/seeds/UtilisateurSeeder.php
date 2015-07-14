<?php

use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		/**
		 *Crée des utilisateurs fictifs.
		 *
		 */
		 
		$nb_admin = 3;
		$nb_tutor = 20;
		$nb_student_classic = 90;
		$nb_student_essa = 10;
		
		
		//on vide toutes les tables  associées aux utilisateurs
		DB::table('utilisateur')->truncate();
		DB::table('utilisateur_ue')->truncate();	
		DB::table('etiquette')->truncate();	
		DB::table('profil_particulier_etudiant')->truncate();	
		
		//création des admin
		for($i = 1; $i <= $nb_admin; $i++)
		{
			$id = 11501000+$i;
			$array_utilisateur_admin_to_insert[] = ['id' => $id,
											  'nom' => 'NomAdmin'.$i,
											  'prenom' => 'PrenomAdmin'.$i,
											  'statut' => 'admin'];
		}
		
		//création des tuteurs

		for($i = 1; $i <= $nb_tutor; $i++)
		{
			$id = 11502000+$i;
			$array_utilisateur_tutor_to_insert[] = ['id' => $id,
											  'nom' => 'NomTuteur'.$i,
											  'prenom' => 'PrenomTuteur'.$i,
											  'statut' => 'tutor'];
									
			//choix de l'ue d'affectation (affectation cyclique)
			$session_scolaire_courrante_id = DB::table('session_scolaire')->whereNull('date_fin')->pluck('id');
			
			$ue_disponibles = DB::table('ue')->where('session_scolaire_id', $session_scolaire_courrante_id);
			$nb_ue_disponibles = $ue_disponibles->count();
			$ue_affectee_id = $ue_disponibles->offset($i % $nb_ue_disponibles)->pluck('id');
			
			$array_utilisateur_ue_to_insert[] = ['utilisateur_id' => $id,
												 'ue_id' =>$ue_affectee_id];
		}

		//création des étudiants
			//étudiants classiques
		for($i = 1; $i <= $nb_student_classic; $i++)
		{
			$id = 11503000+$i;
			$array_utilisateur_student_to_insert[] = ['id' => $id,
											  'nom' => 'NomEtudiant'.$i,
											  'prenom' => 'PrenomEtudiant'.$i,
											  'statut' => 'student',					  
											  'scolarite' => null ];
											  
			$array_etiquette_to_insert[] = ['utilisateur_id' => $id,
											'numero' => 1000 + $i,
											'date_attribution' => time()];
		}
			//étudiants santards
			
				//création du profil santard
		$profil_santard_id = DB::table('profil_particulier_etudiant')->insertGetId(['titre' => 'ESSA', 'mode_calcul' => 'santard']);
		
		for($i = 1; $i <= $nb_student_essa; $i++)
		{
			$id = 11508000+$i;
			$array_utilisateur_student_essa_to_insert[] = ['id' => $id,
											  'nom' => 'Santard'.$i,
											  'prenom' => 'Groconar'.$i,
											  'profil_particulier_etudiant' => $profil_santard_id,
											  'statut' => 'student',
											  'scolarite' => rand(1,2)];
		
			$array_etiquette_essa_to_insert[] = ['utilisateur_id' => $id,
											'numero' => 5000 + $i,
											'date_attribution' => time()];

		}
		
		DB::table('utilisateur')->insert($array_utilisateur_admin_to_insert);
		DB::table('utilisateur')->insert($array_utilisateur_tutor_to_insert);
		DB::table('utilisateur')->insert($array_utilisateur_student_to_insert);
		DB::table('utilisateur')->insert($array_utilisateur_student_essa_to_insert);
		
		DB::table('etiquette')->insert($array_etiquette_to_insert);
		DB::table('utilisateur_ue')->insert($array_utilisateur_ue_to_insert);
    }
}
