<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMS</title>
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet" />
</head>

<body class="antialiased">
    <div id="app"></div>
    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
</body>

</html>
