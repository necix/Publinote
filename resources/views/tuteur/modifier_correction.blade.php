@extends('tuteur.template')

@section('head_script')
		<script type="text/javascript">
			$.ajaxSetup({
			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
				});
		</script>
@stop


@section('title')
Modification de la correction
@stop

@section('contenu')
	@if($obsolete == true)
		<div class="col-sm-offset-3 col-sm-6" >
			<div class="alert alert-warning" role="alert" class="flashinfo">Le classement est désormais obsolète, veuillez le mettre à jour après avoir terminé les modifications de la correction</div>
		</div>
	@endif
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<th> Numéro</th>
			<th> Barème</th>
			<th> Correction </th>
			<th> Action</th>
		</thead>
		<tbody>
		@foreach($corrections as $correction)
			<tr>
				<td> {{ $correction->numero }}</td>
				@if($correction->annule == 1)
				<td colspan="2">Annulé</td>
				@else
				<td> {{ $correction->bareme_titre }}</td>
				<td> {{ Test::compactQCM([$correction->correction_item_a, $correction->correction_item_b, $correction->correction_item_c, $correction->correction_item_d, $correction->correction_item_e]) }} </td>
				@endunless
				<td> 
				{!! Form::open(['url' => 'espace_tuteur/epreuve/supprimer_qcm/']) !!}
					<input type="hidden" name="epreuve_id" value="{{ $epreuve_id }}" />
					<input type="hidden" name="numero_qcm" value="{{ $correction->numero }}" />
					{!! Form::submit('Supprimer', ['class' => 'btn btn-warning']) !!}
				{!! Form::close() !!} 
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	
					
					{{--Message flash qui disparait tout seul--}}
					@if(Session::has('flash_message_qcm'))
					<div class="col-sm-offset-3 col-sm-6" >
						<div class="flashinfo"><div class="alert alert-success" role="alert" class="flashinfo">{{ session('flash_message_qcm') }}</div></div>
					</div>
					@endif
					{!! Form::open(['url' => 'espace_tuteur/epreuve/ajouter_qcm/', 'class'=>'col-sm-offset-2 col-sm-8' ]) !!}
						<input type="hidden" name="epreuve_id" value="{{ $epreuve_id }}" />
						
						<label for="numero_qcm" > Numéro : </label>
						<input type="number" min="1" max="100" required value="{{count($corrections) != 0 ? last($corrections)->numero+1 : 1}}" name="numero_qcm" id="numero" @if(Session::has('flash_message_qcm')) autofocus @endif/> 
						{{-- autofocus si ajout ou suppression de QCM --}}
						
						<label for="bareme" > Barème : </label>
						<select name="bareme_id" id="bareme">
							@foreach($baremes as $bareme)
							<option value="{{ $bareme->id }}"> {{ $bareme->titre }} </option>
							@endforeach
						</select>
						
						<label for="item_a"> A : </label>
						<select name="item_a"  id="item_a">
							<option value="0"> Faux </option>
							<option value="1"> Vrai </option>
							<option value="2"> Vrai/Faux </option>
						</select>
						
						<label for="item_b"> B : </label>
						<select name="item_b" id="item_b">
							<option value="0"> Faux </option>
							<option value="1"> Vrai </option>
							<option value="2"> Vrai/Faux </option>
						</select>
						
						<label for="item_c"> C : </label>
						<select name="item_c" id="item_c">
							<option value="0"> Faux </option>
							<option value="1"> Vrai </option>
							<option value="2"> Vrai/Faux </option>
						</select>
						
						<label for="item_d"> D : </label>
						<select name="item_d" id="item_d">
							<option value="0"> Faux </option>
							<option value="1"> Vrai </option>
							<option value="2"> Vrai/Faux </option>
						</select>
						
						<label for="item_e"> E : </label>
						<select name="item_e" id="item_e">
							<option value="0"> Faux </option>
							<option value="1"> Vrai </option>
							<option value="2"> Vrai/Faux </option>
						</select>
						
						<input type="checkbox" name="annule" id="annule" /> <label for="annule">Annulé</label>
					<input type="submit" value="Ajouter">
				{!! Form::close() !!}

@stop