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

	
			@if($parameters_defined)
				<br />
				{{ General::profilIdToTitle($user_profile) }} <br/>
				{{ General::scolariteIdToTitle($user_scolarite) }}
			@else
				{!! Form::open(['url' => 'parametres']) !!}
					<select name="scolarite">
						<option value="1"> {{ General::scolariteIdToTitle(1) }} </option>
						<option value="2"> {{ General::scolariteIdToTitle(2) }} </option>
						<option value="3"> {{ General::scolariteIdToTitle(3) }} </option>
					</select>
					<select name="profil">
						<option value="0"> Classique </option>
						@foreach($profiles as $profil)
						<option value="{{ $profil->id }}"> {{ $profil->titre }}
						@endforeach
					</select>
					{!! Form::submit('Enregistrer les modifications définitivement', ['class' => 'btn btn-info']) !!}
				{!! Form::close() !!}
			@endif
@stop