<div style="display:flex;gap:12px;flex-wrap:wrap">
 <label>Název<br><input name="name" value="{{ old('name', $course->name ?? '') }}" required></label>
 <label>Kód<br><input name="code" value="{{ old('code', $course->code ?? '') }}" required></label>
 <label>Kapacita<br><input name="capacity" inputmode="numeric" value="{{ old('capacity', $course->capacity ?? '') }}" required></label>
 <label>Začátek<br><input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($course) ? $course->starts_at->format('Y-m-d\TH:i') : '') }}" required></label>
</div>
