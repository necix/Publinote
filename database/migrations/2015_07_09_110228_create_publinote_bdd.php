<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublinoteBdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    //table profil_etudiant
        Schema::dropIfExists('profil_etudiant');
		Schema::create('profil_etudiant', function(Blueprint $table) {
			$table->increments('id');
			$table->string('titre', 20);
			$table->string('mode_calcul', 20);
		});
       
	   //table utilisateur
        Schema::dropIfExists('utilisateur');
		Schema::create('utilisateur', function(Blueprint $table) {
			$table->mediumInteger('id')->unsigned();
			$table->primary('id');
			$table->string('nom', 25);
			$table->string('prenom', 25);
			$table->string('statut', 10);
			$table->tinyInteger('scolarite')->unsigned()->nullable();
			$table->tinyInteger('profil_etudiant')->references('id')->on('profil_etudiant')->nullable();
		});
		
		//table etiquette
        Schema::dropIfExists('etiquette');
		Schema::create('etiquette', function(Blueprint $table) {
			$table->smallInteger('numero')->unsigned();
			$table->primary('numero');
			$table->timestamp('date_attribution');
			$table->integer('utilisateur_id')->references('id')->on('utilisateur');
		});
		
	    //table session_scolaire
        Schema::dropIfExists('session_scolaire');
		Schema::create('session_scolaire', function(Blueprint $table) {
			$table->increments('id');
			$table->string('titre', 50);
			$table->string('message_accueil')->nullable();
			$table->timestamp('date_debut');
			$table->timestamp('date_fin')->nullable();
			$table->smallInteger('numerus_clausus')->unsigned()->nullable();
			$table->smallInteger('nombre_max_etrangers')->unsigned()->nullable();
		});
		
		//table ue
        Schema::dropIfExists('ue');
		Schema::create('ue', function(Blueprint $table) {
			$table->increments('id');
			$table->string('sigle', 10);
			$table->string('titre', 50);
			$table->integer('session_scolaire_id')->references('id')->on('session_scolaire');
		});
		
		//table utilisateur_ue 
        Schema::dropIfExists('utilisateur_ue');
		Schema::create('utilisateur_ue', function(Blueprint $table) {
			$table->mediumInteger('utilisateur_id')->references('id')->on('utilisateur');
			$table->mediumInteger('ue_id')->references('id')->on('ue');
			$table->primary(['utilisateur_id', 'ue_id']);
		});
		
		//table epreuve
        Schema::dropIfExists('epreuve');
		Schema::create('epreuve', function(Blueprint $table) {
			$table->increments('id');
			$table->mediumInteger('session_scolaire_id')->references('id')->on('session_scolaire');
			$table->mediumInteger('ue_id')->references('id')->on('ue');
			$table->string('titre',20);
			$table->timestamp('date');
			$table->boolean('visible');
		});
		
		//table utilisateur_note_epreuve
        Schema::dropIfExists('utilisateur_note_epreuve');
		Schema::create('utilisateur_note_epreuve', function(Blueprint $table) {
			$table->mediumInteger('utilisateur_id')->references('id')->on('utilisateur');
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->primary(['utilisateur_id', 'epreuve_id']);
			$table->smallInteger('classement')->unsigned();
			$table->float('note_reelle');
			$table->float('note_ajustee')->nullable();
		});
		
		//table grille_qcm
        Schema::dropIfExists('grille_qcm');
		Schema::create('grille_qcm', function(Blueprint $table) {
			$table->mediumInteger('utilisateur_id')->references('id')->on('utilisateur');
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->tinyInteger('numero_QCM')->unsigned();
			$table->primary(['utilisateur_id', 'epreuve_id', 'numero_QCM']);
			$table->boolean('item_a')->default(false);
			$table->boolean('item_b')->default(false);
			$table->boolean('item_c')->default(false);
			$table->boolean('item_d')->default(false);
			$table->boolean('item_e')->default(false);
		});
		
		//table statistiques_epreuve
        Schema::dropIfExists('statistiques_epreuve');
		Schema::create('statistiques_epreuve', function(Blueprint $table) {
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->primary('epreuve_id');
			$table->smallInteger('nb_participants')->nullable();
			$table->float('min');
			$table->float('max');
			$table->float('moy');
			$table->text('repartition_notes'); //à revoir
		});
		
		//table statistiques_qcm
        Schema::dropIfExists('statistiques_qcm');
		Schema::create('statistiques_qcm', function(Blueprint $table) {
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->tinyInteger('numero_QCM')->unsigned();
			$table->primary(['epreuve_id', 'numero_QCM']);
			$table->float('taux_0_discordance');
			$table->float('taux_1_discordance');
			$table->float('taux_2_discordance');
			$table->float('taux_3_discordance');
			$table->float('taux_4_discordance');
			$table->float('taux_5_discordance');
			$table->float('taux_item_a');
			$table->float('taux_item_b');
			$table->float('taux_item_c');
			$table->float('taux_item_d');
			$table->float('taux_item_e');
		});
		
		//table bareme
        Schema::dropIfExists('bareme');
		Schema::create('bareme', function(Blueprint $table) {
			$table->increments('id');
			$table->tinyInteger('0_discordance');
			$table->tinyInteger('1_discordance');
			$table->tinyInteger('2_discordance');
			$table->tinyInteger('3_discordance');
			$table->tinyInteger('4_discordance');
			$table->tinyInteger('5_discordance');
		});
		
		//table correction_qcm
        Schema::dropIfExists('correction_qcm');
		Schema::create('correction_qcm', function(Blueprint $table) {
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->tinyInteger('numero_QCM')->unsigned();
			$table->primary(['epreuve_id', 'numero_QCM']);
			$table->tinyInteger('bareme_id')->refenreces('id')->on('bareme');
			$table->boolean('annule')->default(false);
			$table->boolean('item_a')->nullable();
			$table->boolean('item_b')->nullable();
			$table->boolean('item_c')->nullable();
			$table->boolean('item_d')->nullable();
			$table->boolean('item_e')->nullable();
			$table->timestamp('date_modif');
		});
		
		//table regroupement
        Schema::dropIfExists('regroupement');
		Schema::create('regroupement', function(Blueprint $table) {
			$table->increments('id');
			$table->string('titre', 50);
			$table->timestamp('date');
			$table->boolean('visible');
			$table->smallInteger('session_scolaire_id')->references('id')->on('session');
		});
		
		//table regroupement_epreuve
        Schema::dropIfExists('regroupement_epreuve');
		Schema::create('regroupement_epreuve', function(Blueprint $table) {
			$table->mediumInteger('regroupement_id')->references('id')->on('regroupement');
			$table->mediumInteger('epreuve_id')->references('id')->on('epreuve');
			$table->primary(['regroupement_id', 'epreuve_id']);
		});
		
		//table statistiques_regroupement
        Schema::dropIfExists('statistiques_regroupement');
		Schema::create('statistiques_regroupement', function(Blueprint $table) {
			$table->smallInteger('regroupement_id')->references('id')->on('regroupement');
			$table->primary('regroupement_id');
			$table->smallInteger('nb_participants')->nullable();
			$table->float('min');
			$table->float('max');
			$table->float('moy');
			$table->text('repartition_notes'); //à revoir
		});
		
		//table utilisateur_note_regroupement
        Schema::dropIfExists('utilisateur_note_regroupement');
		Schema::create('utilisateur_note_regroupement', function(Blueprint $table) {
			$table->mediumInteger('utilisateur_id')->references('id')->on('utilisateur');
			$table->mediumInteger('regroupement_id')->references('id')->on('regroupement');
			//$table->primary(['utilisateur_id', 'regroupement_id']);
			$table->smallInteger('classement')->unsigned();
			$table->float('note_totale');
		});
		//définit la clé primaire de utilisateur__etudiant
		 //étrangement ne fonctionne pas avec la fonction primary()
		 DB::statement("ALTER TABLE `utilisateur_note_regroupement` ADD PRIMARY KEY(`utilisateur_id`, `regroupement_id`)");
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil_etudiant');
        Schema::dropIfExists('utilisateur');
        Schema::dropIfExists('etiquette');
        Schema::dropIfExists('session');
        Schema::dropIfExists('ue');
        Schema::dropIfExists('utilisateur_ue');
        Schema::dropIfExists('epreuve');
        Schema::dropIfExists('utilisateur_note_epreuve');
        Schema::dropIfExists('grille_qcm');
        Schema::dropIfExists('statistiques_epreuve');
		Schema::dropIfExists('statistiques_qcm');
        Schema::dropIfExists('bareme');
        Schema::dropIfExists('correction_qcm');
        Schema::dropIfExists('regroupement');
        Schema::dropIfExists('regroupement_epreuve');
        Schema::dropIfExists('statistiques_regroupement');
        Schema::dropIfExists('utilisateur_note_regroupement');
    }
}
