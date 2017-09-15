<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4caf50">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel='shortcut icon' type='image/x-icon' href="{{ asset('img/favicon2.png') }}" />
        <title>Upflame</title>

        <link type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

        <link type="text/css" href="https://fonts.googleapis.com/css?family=Raleway:300,600" rel="stylesheet">

        <link type="text/css" href="{{ asset('css/style.css') }}" rel="stylesheet">
        
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top navbar-transparent">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Upflame</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Upflame+</a></li>
                </ul>
            </div><!-- /.container-fluid -->
        </nav>
        <div class="main">
            <div class="main-2">
                <div class="upload-container">

                    @yield('upload-formx');

                </div>           
            </div>
            <div class="background-desc-container">
                <p class="background-desc"></p>
            </div>
        </div>
        <div class="row">
            <div class="feature-desc">
            </div>
        </div>
        <div class="row">
            <div class="premium-feature-desc">
            </div>
        </div>
        <div class="row row-footer">
            <div class="footer">
                <div class="col-md-4">
                    <div id="footer-about">
                        <h4>About</h4>
                        <p>Upflame helps you sharing file easily with lots of options.</p>
                        <p>&copy; Oger {{date('Y')}}  </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="footer-contact">
                        <h4>Contact Us</h4>
                        <p>Contact us at: cs@upfla.me</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="footer-powered">
                        <h4>Credit</h4>
                        <p>Background provided by <a href="http://www.bing.com/gallery/">Bing Image</a></p>
                        <p>Files icon by <a href="http://www.flaticon.com/authors/dimitry-miroliubov">Dimitry Miroliubov</a> from <a href="flaticon.com">FlatIcon.com</a></p>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="{{ asset('js/site.js') }}"></script>
    </body>
</html>
