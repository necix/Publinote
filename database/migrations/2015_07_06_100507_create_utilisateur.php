<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilisateur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		//table utilisateur
        Schema::dropIfExists('utilisateur');
		Schema::create('utilisateur', function(Blueprint $table) {
			$table->mediumInteger('utilisateur_ID')->unsigned();
			$table->primary('utilisateur_ID');
			$table->string('utilisateur_Nom', 25);
			$table->string('utilisateur_Prenom', 25);
			$table->string('utilisateur_AdresseMail', 100);
			
		});
		
		//ref__utilisateur__equipe_Role
		Schema::dropIfExists('ref__utilisateur__equipe_Role');
		Schema::create('ref__utilisateur__equipe_Role', function(Blueprint $table) {
			$table->increments('ref__utilisateur__equipe_Role_ID');
			$table->string('ref__utilisateur__equipe_Role', 50);
			
		});
		
		//ref__programme
		Schema::dropIfExists('ref__programme');
		Schema::create('ref__programme', function(Blueprint $table) {
			$table->increments('ref__programme_ID');
			$table->smallInteger('ref__programme_BordureGauche')
					->references('ref__programme_ID')->on('ref__programme')
					->onDelete('cascade');
			$table->smallInteger('ref__programme_BordureDroite')
					->references('ref__programme_ID')->on('ref__programme')
					->onDelete('cascade');
			$table->string('ref__programme_Categorie', 30);
			$table->string('ref__programme_Sigle', 30);
			$table->string('ref__programme_Intitule', 30);
		});
		
		//utilisateur__equipe
		Schema::dropIfExists('utilisateur__equipe');
        Schema::create('utilisateur__equipe', function(Blueprint $table) {
			$table->mediumInteger('utilisateur__equipe_RefUtilisateur')->unsigned()
				  ->references('utilisateur_ID')->on('utilisateur')
				  ->onDelete('cascade');
			$table->primary('utilisateur__equipe_RefUtilisateur');
			$table->tinyInteger('utilisateur__equipe_Role')->unsigned()
				  ->references('ref__utilisateur__equipe_Role_ID')->on('ref__utilisateur__equipe_Role')
				  ->onDelete('cascade')
				  ->unsigned();
			$table->smallInteger('utilisateur__equipe_Affectation')->unsigned();
		});
		
		//ref__utilisateur__etudiant_Profil
		Schema::dropIfExists('ref__utilisateur__etudiant_Profil');
        Schema::create('ref__utilisateur__etudiant_Profil', function(Blueprint $table) {
			$table->increments('ref__utilisateur__etudiant_Profil_ID');
			$table->string('ref__utilisateur__etudiant_Profil', 10);
		});
		
		// utilisateur__etudiant
		Schema::dropIfExists('utilisateur__etudiant');
        Schema::create('utilisateur__etudiant', function(Blueprint $table) {
			$table->mediumInteger('utilisateur__etudiant_RefUtilisateur')->unsigned()
					->references('utilisateur_ID')->on('utilisateur')
					->onDelete('cascade');
			//$table->primary('utilisateur__etudiant_RefUtilisateur');
			$table->smallInteger('utilisateur__etudiant_Anonymat')->unsigned();
			$table->tinyInteger('utilisateur__etudiant_Profil')->unsigned()
					->references('ref__utilisateur__etudiant_Profil_ID')->on('ref__utilisateur__etudiant_Profil');
			$table->tinyInteger('utilisateur__etudiant_Scolarite')->unsigned();
			;
		 });
		 
		 //définit la clé primaire de utilisateur__etudiant
		 //étrangement ne fonctionne pas avec la fonction primary()
		 DB::statement("ALTER TABLE `utilisateur__etudiant` ADD PRIMARY KEY(`utilisateur__etudiant_RefUtilisateur`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('utilisateur');
		Schema::drop('ref__utilisateur__equipe_Role');
		Schema::drop('ref__programme');
		Schema::drop('utilisateur__Equipe');
		Schema::drop('ref__utilisateur__etudiant_Profil');
		Schema::drop('utilisateur__etudiant');
    }
}
