<!DOCTYPE html>
<html lang="fr">
    <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<meta name="_token" content="{!! csrf_token() !!}"/>
		
		@yield('head_script')
		
		
		<title>@yield('title')</title>
		
		{{--Inclusion des sources de Bootstrap --}}
		{!! HTML::style('https://netdna.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css') !!}
		{!! HTML::style('https://netdna.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css') !!}
		<!--[if lt IE 9]>
			{{ HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') }}
			{{ HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}
		<![endif]-->
		
		<style> textarea { resize: none; } </style>
	
	</head>
	<body>
		
		{{-- Mettre la barre de navigation à partir d'ici. Y insérer les variables ci-dessous au bon endroit ;) --}}
		<a href="{{ url('/') }}"> Accueil </a>
		{{$nb_results_not_read}}
		{{strtoupper(substr($first_name, 0, 1).'. '.$last_name)}}
		<a href="{{ url('/parametres') }}">Parametres</a>
		<a href="{{ url('/aide') }}">Aide</a>
		<a href="{{url('/deconnecter')}}">Se deconnecter</a>
		
		
		{{--Message flash qui disparait tout seul--}}
		@if(Session::has('flash_message'))
		<div class="col-sm-offset-3 col-sm-6" >
			<div class="flashinfo"><div class="alert alert-success" role="alert" class="flashinfo">{{ session('flash_message') }}</div></div>
		</div>
		@endif
		


		@yield('contenu')

		<script>
			$(function(){
								$('.flashinfo').fadeOut(6000)
			});
		</script>

	</body>
</html>