<?php

use Illuminate\Database\Seeder;

class RegroupementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('regroupement')->truncate();
		DB::table('regroupement_epreuve')->truncate();
		DB::table('utilisateur_note_regroupement')->truncate();
		DB::table('statistiques_regroupement')->truncate();
		
		$session_scolaire_courrante_id = DB::table('session_scolaire')->whereNull('date_fin')->pluck('id');
		
        //1 regroupement des 4 premières épreuves
		$regroupement_courrant_id = DB::table('regroupement')->insertGetId(['titre' => 'Regroupement 1',
																		   'date' => time(),
																		   'visible' => true,
																		   'session_scolaire_id' => $session_scolaire_courrante_id]);
		
		for($i = 0; $i < 4; $i++)
		{
			$epreuve_id = DB::table('epreuve')->offset($i)->pluck('id');
			DB::table('regroupement_epreuve')->insert(['regroupement_id' => $regroupement_courrant_id,
													   'epreuve_id' => $epreuve_id,
													   'coefficient' => 20]);
		}
		
		//notes par étudiant
			$etudiants = DB::table('utilisateur')->where('statut','=','student')->get();
			$nb_etudiants = count($etudiants);
			foreach($etudiants as $etudiant)
			{
					$note_max = DB::table('regroupement_epreuve')->where('regroupement_id', $regroupement_courrant_id)->sum('coefficient');
					$note_totale = rand(0, 2000)/2000*$note_max;
					//note et classement
					DB::table('utilisateur_note_regroupement')->insert(['utilisateur_id' => $etudiant->id,
													   'regroupement_id' => $regroupement_courrant_id,
													   'classement' => rand(1, $nb_etudiants),
													   'note_totale' => $note_totale]);
			}	
			
		//statistiques générales du regroupement
		DB::table('statistiques_regroupement')->insert(['regroupement_id' => $regroupement_courrant_id,
														'min' => 0,
														'max' => 17,
														'moy' => 8,
														'nb_participants' => $nb_etudiants,
														'date_generation' => time()+11]);
		
    }
}
