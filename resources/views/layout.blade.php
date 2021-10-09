<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reviz Panel</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/vendor/reviz/favicon.ico')}}">
    <link href="{{ asset(mix('reviz.css', 'vendor/reviz')) }}" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="flex justify-center py-2 mb-10 border-b shadow">
        <a href="" class="flex hover:opacity-50">
            <img src="{{asset('/vendor/reviz/favicon.ico')}}" class="mr-2">
            <h1 class="text-2xl">Reviz Panel</h1>
        </a>
    </div>

    <div class="container mx-auto">
        @yield('content')
    </div>

    <script src="{{asset(mix('reviz.js', 'vendor/reviz'))}}"></script>
</body>
</html>