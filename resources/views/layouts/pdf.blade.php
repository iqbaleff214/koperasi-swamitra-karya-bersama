<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
        integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
</head>

<body style="font-family: sans-serif;">
    <header>
        <img src="{{ public_path('swamitra.jpeg') }}" alt="{{ $title }}" width="50px" class="mb-2" srcset="">
        <span class="d-block h3 mb-0">{{ strtoupper(config('app.name')) }}</span>
        <span class="d-block">Jl. Sesama No. 47 RT. 16</span>
        <span class="d-block">Telp. 62 851-4306-4088</span>
        <hr>
    </header>
    <section>
        <h2 class="mx-auto d-auto text-center mb-3">{{ $title }}</h2>
        @yield('header')
    </section>
    <section class="mt-3">
        @yield('content')
    </section>
    <section class="mt-4">
        <div class="float-right">
            <span class="d-block">Mengetahui</span>
            <span class="d-block"><span style="margin-left: 150px">{{ date('Y') }}</span></span>
            <span class="d-block">Manager</span>
            <span class="d-block" style="margin-top: 75px">{{ $manager->name }}</span>
        </div>
    </section>
</body>

</html>
