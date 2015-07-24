@extends('tuteur.template')


@section('title')
Tableau de bord - Tuteur
@stop

@section('contenu')
	@if(count($tests) == 0)
			<p> Aucune �preuve disponible </p>
	@else
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<th> Date</th>
			<th> Titre </th>
			<th> Catégorie </th>
			<th> Visibilité</th>
			<th> Nb questions</th>
			<th> Nb grilles rendues</th>
		</thead>
		@foreach($tests as $test)
		<tr >
			<td>{{ date('j/m/Y', $test->date) }}</td>
			<td>{{ $test->titre }}</td>
			<td>{{ $test->ue }}</td>
			<td>{{ $test->visible }}</td> 
			<td>{{ Test::nbQCMs($test->id) }} </td>
			<td>{{ Test::nbGrids($test->id) }} </td>
			<td> <a href="{{ url('/espace_tuteur/epreuve/'.$test->id) }}"> Afficher </a> </td>

		</tr>
		@endforeach
	</table>
	@endif

	
@stop