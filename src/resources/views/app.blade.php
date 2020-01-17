<!doctype html>
<html class="has-navbar-fixed-top" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<title>MediaDB</title>
<meta name="description"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ url('favicon.png') }}">
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>

<div id="app"></div>

<script async src="{{ mix('js/app.js') }}"></script>

</body>
</html>
