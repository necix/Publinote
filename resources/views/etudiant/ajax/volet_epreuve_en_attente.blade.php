{{ $test->title }} <br />
{{ $test->category_sigle }}:
{{ $test->category_title }}<br/>
Résultats en attente<br />
<a href="{{ url('/epreuve/'.$test->id ) }}"> Plus d'infos </a>