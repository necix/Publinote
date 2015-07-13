@extends('etudiant.template')

@section('head_script')
		<script type="text/javascript">
			$.ajaxSetup({
			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
				});
		</script>
@stop


@section('title')
{{ $test->title }}
@stop

@section('contenu')
{{ $test->title }} <br />
{{ $test->category_sigle }}:
{{ $test->category_title }}<br/>
	@unless(!$is_corrected)
		{{ $test->rank }}/
		{{ $test->participants }}<br />
		{{ $test->mark_real }} / {{ $test->mark_max }}<br/>
		{{ $test->mark_ajusted }} / {{ $test->mark_max }}<br/>
	@endunless

	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<th> Numéro</th>
			<th> Réponse attendue </th>
			<th> Notation </th>
			<th> Votre réponse </th>
			<th> Note </th>
		</thead>
		@foreach($qcms as $qcm)
		<tr>
			<td> {{ $qcm->numero }} </td>
			@if($qcm->annule)
			<td colspan="4"> Annulé </td>
			@else
			<td> {{ Test::compactQCM([ $qcm->correction_item_a, $qcm->correction_item_b, $qcm->correction_item_c, $qcm->correction_item_d, $qcm->correction_item_e ]) }}</td>
			<td> {{ $qcm->bareme_titre}}</td>
			<td> {{ Test::compactQCM([ $qcm->grille_item_a, $qcm->grille_item_b, $qcm->grille_item_c, $qcm->grille_item_d, $qcm->grille_item_e ]) }}</td>
			<td>{{ Test::discordanceToNote($qcm) }}/{{$qcm->zero_discordance}}</td>
			@endif
		</tr>
		@endforeach
	</table>
@stop