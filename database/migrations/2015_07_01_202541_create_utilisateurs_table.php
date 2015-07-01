<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilisateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilisateurs', function(Blueprint $table) {
		$table->increments('id');
		$table->string('cas_id', 8);
		$table->string('nom', 30);
		$table->string('prenom', 30);
		$table->string('statut_publinote', 10);
		$table->boolean('inscription_manuelle');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('utilisateurs');
    }
}
