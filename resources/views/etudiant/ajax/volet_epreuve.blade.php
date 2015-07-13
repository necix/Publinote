{{ $test->title }} <br />
{{ $test->category_sigle }}:
{{ $test->category_title }}<br/>
{{ $test->rank }}/
{{ $test->participants }}<br />
{{ $test->mark_real }} / {{ $test->mark_max }}<br/>
{{ $test->mark_ajusted }} / {{ $test->mark_max }}<br/>
<a href="{{ url('/epreuve/'.$test->id ) }}"> Plus d'infos </a>
