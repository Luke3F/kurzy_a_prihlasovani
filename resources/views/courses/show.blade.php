@extends('layout')
@section('title', $course->name)
@section('content')
<p><a href="{{ route('courses.index') }}">← Kurzy</a></p>
<h1>{{ $course->name }}</h1>
<p>Obsazenost: <strong>{{ $active }} / {{ $course->capacity }}</strong> (volno {{ max(0, $course->capacity - $active) }})</p>
<form method="POST" action="{{ route('courses.update', $course) }}" class="box">@csrf @method('PUT')
 <strong>Úprava kurzu</strong>@include('courses._form')<button>Uložit změny</button>
</form>
<div class="box"><strong>Přihlášení</strong>
<table><tr><th>Jméno</th><th>E-mail</th><th>Přihlášen</th><th>Stav</th><th></th></tr>
@forelse($course->enrollments as $e)
<tr><td>{{ $e->participant->name }}</td><td>{{ $e->participant->email }}</td><td>{{ $e->enrolled_at->format('j. n. Y H:i') }}</td>
 <td><span class="tag {{ $e->status }}">{{ \App\Models\Enrollment::LABELS[$e->status] }}</span></td>
 <td>
 @foreach(['enrolled' => ['completed' => 'Dokončit', 'cancelled' => 'Zrušit'], 'cancelled' => ['enrolled' => 'Obnovit'], 'completed' => []][$e->status] as $to => $label)
  <form class="i" method="POST" action="{{ route('enrollments.update', $e) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $to }}"><button class="o">{{ $label }}</button></form>
 @endforeach</td></tr>
@empty<tr><td colspan="5">Nikdo není přihlášen.</td></tr>@endforelse
</table>
<form method="POST" action="{{ route('enrollments.store', $course) }}" style="margin-top:10px">@csrf
 <select name="participant_id" required><option value="">Vyberte účastníka…</option>
 @foreach($available as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>@endforeach</select>
 <button @disabled($active >= $course->capacity)>Přihlásit</button>
 @if($active >= $course->capacity)<small>Kurz je obsazen.</small>@endif
</form></div>
<form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('Smazat kurz i s přihláškami?')">@csrf @method('DELETE')<button class="r">Smazat kurz</button></form>
@endsection
