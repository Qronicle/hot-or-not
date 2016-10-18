<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hot or Not | Uni-t</title>

    <!-- Fonts -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
    <header>
        <h1>
            <a href="/">
                <img src="/img/logo.png" alt="Uni-t web communication"/>
                <span><em>Hot</em><br> or Not</span>
            </a>
        </h1>
    </header>
    <div class="content @yield('content-class')">
        @yield('content')
        <footer>
            <ul>
                <li><a href="/">Cast your vote</a></li>
                <li><a href="/add-user">Add a new user</a></li>
                <li><a href="/statistics">Statistics</a></li>
            </ul>
        </footer>
    </div>
    <script src="{{ elixir('js/jquery-3.1.1.min.js') }}"></script>
    @yield('footer')
</body>
</html>
