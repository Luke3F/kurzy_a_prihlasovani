<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Kurzy')</title>
<style>
body{font:15px/1.5 system-ui,sans-serif;margin:0;background:#f2f5f4;color:#16222a}
main{max-width:960px;margin:0 auto;padding:16px}
nav a{margin-right:16px;font-weight:600;color:#0f6e6e;text-decoration:none}
.box{background:#fff;border:1px solid #d3dcdb;border-radius:8px;padding:14px;margin:12px 0}
label{display:block;font-size:.85rem;font-weight:600}input,select{padding:7px;margin:3px 0 8px;border:1px solid #bcc8c7;border-radius:5px;font:inherit}
button{padding:7px 12px;border:1px solid #0f6e6e;background:#0f6e6e;color:#fff;border-radius:5px;cursor:pointer;font:inherit}
button.o{background:#fff;color:#0f6e6e}button.r{background:#fff;color:#b3261e;border-color:#b3261e}
table{border-collapse:collapse;width:100%}th,td{padding:6px;border-bottom:1px solid #d3dcdb;text-align:left}
.err{background:#fbe3e1;color:#b3261e;padding:10px;border-radius:6px}.ok{background:#dff1e4;color:#1f7a3d;padding:10px;border-radius:6px}
.tag{padding:1px 8px;border-radius:99px;background:#dcefee}.cancelled{background:#fbe3e1}.completed{background:#dff1e4}
form.i{display:inline}
</style>
</head>
<body><main>
<nav><a href="{{ route('courses.index') }}">Kurzy</a><a href="{{ route('participants.index') }}">Účastníci</a></nav>
@if(session('ok'))<p class="ok">{{ session('ok') }}</p>@endif
@if($errors->any())<div class="err" role="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
@yield('content')
</main></body></html>
