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
			<th>  </th>
		</thead>
		@foreach($tests_with_mark as $test)
		<tr>
			<td>{{ date('j/m/Y', $test->date_test) }}</td>
			<td>{{ $test->title }}</td>
			<td>{{ $test->category }}</td>
			<td>{{ $test->rank }}/{{ $test->participants }}</td> 
			<td>{{ $test->read }} </td>
			<td>{{ $test->id }}</td>
		</tr>
		@endforeach
		
		@foreach($tests_without_mark as $test)
		<tr>
			<td>{{ date('j/m/Y', $test->date_test) }}</td>
			<td>{{ $test->title }}</td>
			<td>{{ $test->category }}</td>
			<td>En attente</td> 
			<td></td>
			<td>{{ $test->id }}</td>
		</tr>
		@endforeach
	</table>
	@endif
	
@stop