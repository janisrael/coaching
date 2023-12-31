<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-token" content="{{ $appToken ?? "" }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script>
        let APP_TOKEN = '{{ $appToken ?? "" }}';
    </script>
    <script src="{{ asset('js/app.js').'?d='.time() }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    {{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    <style>
        html, body {
            /*background-color: #fff;*/
            /*color: #636b6f;*/
            font-family: sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        /*.links > a {*/
        /*    color: #636b6f;*/
        /*    padding: 0 25px;*/
        /*    font-size: 13px;*/
        /*    font-weight: 600;*/
        /*    letter-spacing: .1rem;*/
        /*    text-decoration: none;*/
        /*    text-transform: uppercase;*/
        /*}*/

        /*.m-b-md {*/
        /*    margin-bottom: 30px;*/
        /*}*/
    </style>
</head>
<body>
    <div id="app">
        {{--        <h1 class="text-center">{{ config('app.name') }}</h1>--}}
        {{--        <h3 class="text-center">Theme one</h3>--}}
        {{--        <p class="text-center">/{{ request()->segment(2) }}</p>--}}
        <index></index>
    </div>

    <script>
        window.ENV = {
            APP_URL: '{{ config('app.coaching_url') }}',
            APP_DEBUG: '{{ config('app.debug') }}'
            // PARENT_URL: '{{ config('app.parent_url') }}' 
        };
    </script>
</body>
</html>
