@extends('layout')
@section('title', 'Účastníci')
@section('content')
<h1>Účastníci</h1>
<form method="POST" action="{{ route('participants.store') }}" class="box">@csrf
 <strong>Nový účastník</strong><br>
 <label style="display:inline-block">Jméno<br><input name="name" value="{{ old('name') }}" required></label>
 <label style="display:inline-block">E-mail<br><input type="email" name="email" value="{{ old('email') }}" required></label>
 <button>Přidat účastníka</button>
</form>
<div class="box"><table><tr><th>Jméno</th><th>E-mail</th><th>Aktivní kurzy</th><th></th></tr>
@forelse($participants as $p)
<tr><td>{{ $p->name }}</td><td>{{ $p->email }}</td><td>{{ $p->active_count }}</td><td>
 <a href="{{ route('participants.edit', $p) }}">Upravit</a>
 <form class="i" method="POST" action="{{ route('participants.destroy', $p) }}" onsubmit="return confirm('Smazat účastníka i s přihláškami?')">@csrf @method('DELETE')<button class="r">Smazat</button></form></td></tr>
@empty<tr><td colspan="4">Žádní účastníci.</td></tr>@endforelse
</table></div>
@endsection
