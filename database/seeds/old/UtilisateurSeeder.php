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
		
		//on vide toutes les tables préexistantes
		DB::table('utilisateur')->truncate();
		DB::table('utilisateur__equipe')->truncate();
		DB::table('utilisateur__etudiant')->truncate();
		DB::table('ref__utilisateur__equipe_Role')->truncate();
		DB::table('ref__programme')->truncate();
		DB::table('ref__utilisateur__etudiant_Profil')->truncate();
        
		//création de 3 admin
		DB::table('ref__utilisateur__equipe_Role')->insert(['ref__utilisateur__equipe_Role' => 'admin']);
		$ref_admin_id = DB::table('ref__utilisateur__equipe_Role')->where('ref__utilisateur__equipe_Role', 'admin')->pluck('ref__utilisateur__equipe_Role_ID');
		
		for($i = 1; $i < 4; $i++)
		{
			$id = 11501000+$i;
			DB::table('utilisateur')->insert(['utilisateur_ID' => $id,
											  'utilisateur_Nom' => 'NomAdmin'.$i,
											  'utilisateur_Prenom' => 'PrenomAdmin'.$i]);
											  
			DB::table('utilisateur__equipe')->insert(['utilisateur__equipe_RefUtilisateur' => $id,
													  'utilisateur__equipe_role' =>$ref_admin_id]);
		}
		
		//création de 20 tuteurs
		DB::table('ref__utilisateur__equipe_Role')->insert(['ref__utilisateur__equipe_Role' => 'tuteur']);
		$ref_tuteur_id = DB::table('ref__utilisateur__equipe_Role')->where('ref__utilisateur__equipe_Role', 'tuteur')->pluck('ref__utilisateur__equipe_Role_ID');
		
		for($i = 1; $i < 21; $i++)
		{
			$id = 11502000+$i;
			DB::table('utilisateur')->insert(['utilisateur_ID' => $id,
											  'utilisateur_Nom' => 'NomTuteur'.$i,
											  'utilisateur_Prenom' => 'PrenomTuteur'.$i]);
											  
			DB::table('utilisateur__equipe')->insert(['utilisateur__equipe_RefUtilisateur' => $id,
													  'utilisateur__equipe_role' =>$ref_tuteur_id]);
		}
		
		//création de 100 étudiants
			//90 classiques
		DB::table('ref__utilisateur__etudiant_Profil')->insert(['ref__utilisateur__etudiant_Profil' => 'classique']);
		$ref_classique_id = DB::table('ref__utilisateur__etudiant_Profil')->where('ref__utilisateur__etudiant_Profil', 'classique')->pluck('ref__utilisateur__etudiant_Profil_ID');

		for($i = 1; $i < 91; $i++)
		{
			$id = 11503000+$i;
			DB::table('utilisateur')->insert(['utilisateur_ID' => $id,
											  'utilisateur_Nom' => 'NomEtudiant'.$i,
											  'utilisateur_Prenom' => 'PrenomEtudiant'.$i]);
											  
			DB::table('utilisateur__etudiant')->insert(['utilisateur__etudiant_RefUtilisateur' => $id,
													  'utilisateur__etudiant_Profil' =>$ref_classique_id,
													  'utilisateur__etudiant_Anonymat' =>1000+$i,
													  'utilisateur__etudiant_Scolarite' =>rand(1,2)]);
		}
			//10 santards
		DB::table('ref__utilisateur__etudiant_Profil')->insert(['ref__utilisateur__etudiant_Profil' => 'santard']);
		$ref_santard_id = DB::table('ref__utilisateur__etudiant_Profil')->where('ref__utilisateur__etudiant_Profil', 'santard')->pluck('ref__utilisateur__etudiant_Profil_ID');
				for($i = 1; $i < 11; $i++)
		{
			$id = 11504000+$i;
			DB::table('utilisateur')->insert(['utilisateur_ID' => $id,
											  'utilisateur_Nom' => 'Groconar'.$i,
											  'utilisateur_Prenom' => 'Santard'.$i]);
											  
			DB::table('utilisateur__etudiant')->insert(['utilisateur__etudiant_RefUtilisateur' => $id,
													  'utilisateur__etudiant_Profil' =>$ref_santard_id,
													  'utilisateur__etudiant_Anonymat' =>2000+$i,
													  'utilisateur__etudiant_Scolarite' =>rand(1,2)]);
		}
    }
}
