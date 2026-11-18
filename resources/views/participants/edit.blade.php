@extends('layout')
@section('title', 'Úprava účastníka')
@section('content')
<h1>Úprava účastníka</h1>
<form method="POST" action="{{ route('participants.update', $participant) }}" class="box">@csrf @method('PUT')
 <label>Jméno<br><input name="name" value="{{ old('name', $participant->name) }}" required></label>
 <label>E-mail<br><input type="email" name="email" value="{{ old('email', $participant->email) }}" required></label>
 <button>Uložit změny</button> <a href="{{ route('participants.index') }}">Zpět</a>
</form>
@endsection
