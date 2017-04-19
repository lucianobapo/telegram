<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
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

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>

        <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                var mensagens = "/get-updates";
                $.get(mensagens, function (data) {
                    $.each(data, function (i, item) {
                        $(".mensagens").append("<div>" + item.message.text + "</div><hr>");
                    });
                });
            });
        </script>
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                {!! \Form::open(['url' => '/send-message', 'class' => 'form-signin']) !!}
                <h2 class="form-signin-heading">Enviar a mensagem</h2>
                <label for="inputText" class="sr-only">Message</label>
                <textarea name="message" type="text" id="inputText" class="form-control" placeholder="Message" required autofocus></textarea>
                <br />
                <button class="btn btn-lg btn-primary btn-block" type="submit">Enviar</button>
                {!! \Form::close() !!}
                <br />

                <h2 class="form-signin-heading">Mensagems</h2>
                <br />
                <div class="panel panel-primary">

                    <div class="panel-heading"> <h3 class="panel-title">Mensagens</h3> </div>
                    <div class="panel-body">
                        <div class="mensagens"></div>
                    </div>

                </div>

            </div>
        </div>


        {{--<div class="flex-center position-ref full-height">--}}
            {{--@if (Route::has('login'))--}}
                {{--<div class="top-right links">--}}
                    {{--@if (Auth::check())--}}
                        {{--<a href="{{ url('/home') }}">Home</a>--}}
                    {{--@else--}}
                        {{--<a href="{{ url('/login') }}">Login</a>--}}
                        {{--<a href="{{ url('/register') }}">Register</a>--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--@endif--}}

            {{--<div class="content">--}}
                {{--<div class="title m-b-md">--}}
                    {{--Laravel--}}
                {{--</div>--}}

                {{--<div class="links">--}}
                    {{--<a href="https://laravel.com/docs">Documentation</a>--}}
                    {{--<a href="https://laracasts.com">Laracasts</a>--}}
                    {{--<a href="https://laravel-news.com">News</a>--}}
                    {{--<a href="https://forge.laravel.com">Forge</a>--}}
                    {{--<a href="https://github.com/laravel/laravel">GitHub</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </body>
</html>
