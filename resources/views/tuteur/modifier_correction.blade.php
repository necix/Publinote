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
				<td> <a href="#"> Modifier </a> 
					<div class="btn btn-warning supprimer_qcm" test_id="{{$correction->epreuve_id}}" numero_qcm="{{ $correction->numero }}"> Supprimer </div> 
				</td>
			</tr>
		@endforeach
		</tbody>

<script>
	$(function(){
		$('.supprimer_qcm').each(function(){
							$(this).click(function(){ 
																
																$.ajax({
																   url : '{{ url("/delete_qcm")}}',
																   type : 'POST',
																   dataType : "html",
																   data : "",
																   success : function(code_html, statut){
																	  
																   },

																   error : function(resultat, statut, erreur){
																	  
																   },

																   complete : function(resultat, statut){
																	  
																   }

																	});
   
													});
							});
	});
</script>
@stop