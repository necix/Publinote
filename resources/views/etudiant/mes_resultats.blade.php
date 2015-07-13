@extends('etudiant.template')

@section('head_script')
		<script type="text/javascript">
			$.ajaxSetup({
			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
				});
		</script>
@stop


@section('title')
@if($nb_results_not_read != 0)
({{ $nb_results_not_read }})
@endif
Mes résultats 
@stop

@section('contenu')

	@unless($home_message == '')
		{{ $home_message }}
	@endunless
	
	@if(count($tests_with_mark) + count($tests_without_mark) == 0)
			<p> Vous n'avez pas encore de note </p>
	@else
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<th> Date</th>
			<th> Titre </th>
			<th> Catégorie </th>
			<th> Classement </th>
			<th> Lu ? </th>
		</thead>
		@foreach($tests_with_mark as $test)
		<tr class="test_grouping_line"  test_grouping_type='test' test_grouping_id='{{ $test->id }}'>
			<td>{{ date('j/m/Y', $test->date_test) }}</td>
			<td>{{ $test->title }}</td>
			<td>{{ $test->category }}</td>
			<td>{{ $test->rank }}/{{ $test->participants }}</td> 
			<td>{{ $test->read }} </td>

		</tr>
		@endforeach
		
		@foreach($tests_without_mark as $test)
		<tr class="test_grouping_line"  test_grouping_type='test_pending' test_grouping_id='{{ $test->id }}' >
			<td>{{ date('j/m/Y', $test->date_test) }}</td>
			<td>{{ $test->title }}</td>
			<td>{{ $test->category }}</td>
			<td>En attente</td> 
			<td></td>
		</tr>
		@endforeach
	</table>
	@endif
	
		<table class="table table-bordered table-striped table-condensed">
		<thead>
			<th> Date</th>
			<th> Titre </th>
			<th> Catégorie </th>
			<th> Classement </th>
			<th> Lu ? </th>
		</thead>
		@foreach($groupings as $grouping)
		<tr class="test_grouping_line"  test_grouping_type='grouping' test_grouping_id='{{ $grouping->id }}' >
			<td>{{ date('j/m/Y', $grouping->date_grouping) }}</td>
			<td>{{ $grouping->title }}</td>
			<td>
				@foreach($grouping_categories[$grouping->id] as $category)
					{{ $category }}
				@endforeach
			</td>
			<td>{{ $grouping->rank }}/{{ $grouping->participants }}</td> 
			<td>{{ $grouping->read }} </td>

		</tr>
		@endforeach
	</table>
	
	<div id="panel_epreuve" class="panel panel-info">
		Cliquez sur une épreuve pour afficher plus de détails.
	</div>
	
<script>
	$(function(){
		$('.test_grouping_line').each(function(){
							$(this).click(function(){ 
																$("#panel_epreuve").slideUp();
																$.ajax({
																   url : '{{ url("/volet_epreuve")}}',
																   type : 'POST',
																   dataType : "html",
																   data : 'test_grouping_type=' + $(this).attr('test_grouping_type') + '&test_grouping_id=' + $(this).attr('test_grouping_id'),
																   success : function(code_html, statut){
																	   $("#panel_epreuve").html(code_html);
																   },

																   error : function(resultat, statut, erreur){
																		$("#panel_epreuve").html("<p>Erreur à la récupération des résultats, veuillez vérifer votre connexion. </p>")
																   },

																   complete : function(resultat, statut){
																	$("#panel_epreuve").slideDown();
																   }

																	});
   
													});
							});
	})
</script>
@stop