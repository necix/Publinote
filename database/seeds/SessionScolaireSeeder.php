<?php

use Illuminate\Database\Seeder;

class SessionScolaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Définit la session scolaire
		DB::table('session_scolaire')->truncate();
		DB::table('session_scolaire')->insert([
												'titre' => 'Année 2015/2016',
												'message_accueil' => 'Bienvenue sur publinote',
												'date_debut' => time(),
												'numerus_clausus' => 9,
												]);
		$session_scolaire_id = DB::table('session_scolaire')->whereNull('date_fin')->pluck('id');
		//Definit la table des UE
		DB::table('ue')->truncate();
		DB::table('ue')->insert([
									['sigle' => 'UE 1',
									'titre' => 'Chimie, biochimie, biologie moléculaire',
									'session_scolaire_id' => $session_scolaire_id,
									'note_max' => 20],
									['sigle' => 'UE 2',
									'titre' => 'Biologie cellulaire',
									'session_scolaire_id' => $session_scolaire_id,
									'note_max' => 10],
									['sigle' => 'UE 3',
									'titre' => 'Biophysique',
									'session_scolaire_id' => $session_scolaire_id,
									'note_max' => 12],
									['sigle' => 'UE 4',
									'titre' => 'Biostatistiques',
									'session_scolaire_id' => $session_scolaire_id,
									'note_max' => 8]
								]);
		
		//Définit les différents barèmes
		DB::table('bareme')->truncate();
		DB::table('bareme')->insert([
										['titre' => '5/3/1',
										 '0_discordance' => 5,
										 '1_discordance' => 3,
										 '2_discordance' => 1,
										 '3_discordance' => 0,
										 '4_discordance' => 0,
										 '5_discordance' => 0],
										 
										 ['titre' => '5/0',
										 '0_discordance' => 5,
										 '1_discordance' => 0,
										 '2_discordance' => 0,
										 '3_discordance' => 0,
										 '4_discordance' => 0,
										 '5_discordance' => 0]
									]);
									
    }
}
