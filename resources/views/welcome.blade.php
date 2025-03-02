<!-- resources/views/layouts/app.blade.php -->

<html>
    <head>
        <title> @yield('title')</title>
        @yield('css')
        <meta name="csrf-token" content="{{ csrf_token() }}">

    </head>
    <body>
        @section('sidebar')
           
        @show

        <div class="container">
            @yield('content')
        </div>
        <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
        @yield('js')

    </body>
</html>