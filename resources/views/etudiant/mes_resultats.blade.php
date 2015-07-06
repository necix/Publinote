@extends('etudiant.template')

@section('head_script')
		<script type="text/javascript">
			$.ajaxSetup({
			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
				});
		</script>
@stop


@section('title')
Mes résultats 
@if($nb_results != 0)
({{ $nb_results }})
@endif
@stop

@section('contenu')

	@unless($home_message == '')
		{{ $home_message }}
	@endunless
	
	@if(count($tests) == 0)
			<p> Vous n'avez pas encore de note </p>
	@else
		@foreach($tests as $test)
			{{ $test['date_creation'] }}
			{{ $test['title'] }}
			{{ $test['category'] }}
			{{ $test['status'] }}
			{{ $test['rank'] }} 
			{{ $test['participants'] }} 
			{{ $test['read'] }} 
			{{ $test['id'] }}
		@endforeach
	@endif
	
@stop