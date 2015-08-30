@extends('tuteur.template')


@section('title')
Épreuve - Tuteur
@stop

@section('contenu')

@if($isTutorTest == true)
	@if($nbQCMs == 0)
	<p><a href="{{url('/espace_tuteur/epreuve/editer_correction/'.$test->id)}}">Ajouter une correction</a></p>
	@else
	<p><a href="{{url('/espace_tuteur/epreuve/editer_correction/'.$test->id)}}">Modifier la correction</a></p>
	@endif
@endif

<h3>Infos générales</h3>
Titre : {{$test->titre }}<br />
UE : {{ $test->ue }}<br />
Date de l'épreuve : {{ date('j/m/Y', $test->date) }}<br />
Visibilité : @if($test->visible == 1)
				Affichée
				@else
				Masquée
				@endif<br />
<h4>Tuteurs de l'épreuve</h4>
@foreach($tutors as $tutor) {{$tutor->first_name.' '.$tutor->last_name}} <br />@endforeach

<h3>Statistiques</h3>
	@if($obsolete == true)
		<div class="col-sm-offset-3 col-sm-6" >
			<div class="alert alert-warning" role="alert" class="flashinfo">Le classement est désormais obsolète, veuillez le mettre à jour après avoir terminé les modifications de la correction</div>
		</div>
	@endif
@if($nbGrids == 0)
Pas de statistiques disponibles, aucune grille corrigée.
@else
Nb participants : {{ $test->nb_participants}} <br />
Moyenne : {{$test->note_moy }} <br />
Note min : {{$test->note_min }} <br />
Note max : {{$test->note_max }} <br />
@endif

<h3>Correction @unless($nbGrids == 0) + Stats par QCM @endunless</h3>  
@if($nbQCMs == 0)
Pas de correction disponible
@else
<table class="table table-bordered table-striped table-condensed">
	<thead>
		<th>Numéro QCM</th>
		<th>Correction</th>
		@unless($nbGrids == 0)
		<th>A(taux réussite)</th>
		<th>B</th>
		<th>C</th>
		<th>D</th>
		<th>E</th>
		<th>5(items justes)</th>
		<th>4</th>
		<th>3</th>
		<th>2</th>
		<th>1</th>
		<th>0</th>
		@endunless
	</thead>
	<tbody>
		@foreach($qcmGrid as $qcm)
		<tr>
			<td>{{ $qcm->numero }}</td>
			@if($qcm->annule == 1)
			<td colspan="12">Annulé</td>
			@else
			<td>{{ Test::compactQCM([$qcm->correction_item_a, $qcm->correction_item_b, $qcm->correction_item_c, $qcm->correction_item_d, $qcm->correction_item_e]) }}</td>
			@unless($nbGrids == 0)
			<td>{{ $qcm->taux_reussite_item_a * 100}} %</td>
			<td>{{ $qcm->taux_reussite_item_b * 100}} %</td>
			<td>{{ $qcm->taux_reussite_item_c * 100}} %</td>
			<td>{{ $qcm->taux_reussite_item_d * 100}} %</td>
			<td>{{ $qcm->taux_reussite_item_e * 100}} %</td>
			<td>{{ $qcm->taux_0_discordance * 100}} %</td>
			<td>{{ $qcm->taux_1_discordance * 100}} %</td>
			<td>{{ $qcm->taux_2_discordance * 100}} %</td>
			<td>{{ $qcm->taux_3_discordance * 100}} %</td>
			<td>{{ $qcm->taux_4_discordance * 100}} %</td>
			<td>{{ $qcm->taux_5_discordance * 100}} %</td>
			@endunless
			@endif
		</tr>
		@endforeach
	</tbody>
@endunless


	
@stop
