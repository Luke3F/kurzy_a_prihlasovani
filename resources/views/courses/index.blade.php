@extends('layout')
@section('title', 'Kurzy')
@section('content')
<h1>Kurzy</h1>
<form method="POST" action="{{ route('courses.store') }}" class="box">@csrf
 <strong>Nový kurz</strong>@include('courses._form')<button>Přidat kurz</button>
</form>
<form method="GET" class="box"><strong>Filtr</strong><br>
 <label style="display:inline-block">Od <input type="date" name="from" value="{{ request('from') }}"></label>
 <label style="display:inline-block">Do <input type="date" name="to" value="{{ request('to') }}"></label>
 <label style="display:inline-block"><input type="checkbox" name="free" value="1" @checked(request()->boolean('free'))> Jen volná místa</label>
 <button class="o">Filtrovat</button> <a href="{{ route('courses.index') }}">Zrušit</a>
</form>
<div class="box"><table>
 <tr><th>Název</th><th>Kód</th><th>Začátek</th><th>Obsazeno</th><th></th></tr>
 @forelse($courses as $c)
 <tr><td>{{ $c->name }}</td><td>{{ $c->code }}</td><td>{{ $c->starts_at->format('j. n. Y H:i') }}</td>
  <td>{{ $c->active_count }} / {{ $c->capacity }} @if($c->active_count >= $c->capacity)<span class="tag">obsazeno</span>@else(volno {{ $c->capacity - $c->active_count }})@endif</td>
  <td><a href="{{ route('courses.show', $c) }}">Detail</a></td></tr>
 @empty<tr><td colspan="5">Žádné kurzy.</td></tr>@endforelse
</table></div>
@endsection
