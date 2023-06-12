<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LOS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300;1,400&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/public/favicon.png">
    @vite(['resources/scss/app.scss'])
</head>
<body>
<div class="container-md d-flex w-100 flex-column">
    @yield('content')
</div>
</body>
</html>
