<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('media/img/icon-512x512-32x32.png') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- datatable css -->
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/plug-ins/1.10.20/integration/font-awesome/dataTables.fontAwesome.css" rel="stylesheet">

    <!-- SELECT2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://kit.fontawesome.com/4e337af5d8.js" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <!-- datatable javascript -->
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    @yield('scripts')
    <title>@yield('title')</title>
</head>
    <body>

        <div class="d-flex" id="wrapper">

            <!-- Sidebar -->
            <div class="sidebar border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-center">
                <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('img/logo.png') }}" alt="QddApp"></a>
            </div>
            <div class="list-group list-group-flush">
               
                
                <a href="{{ route('admins.index') }}" class="list-group-item list-group-item-action text-blue lead"><i class="fas fa-users-cog"></i> {{ __('Administradores') }}</a>
                <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action text-blue lead"><i class="fas fa-users"></i> {{ __('Usuarios') }}</a>
                <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action text-blue lead"><i class="fas fa-th-list"></i> {{ __('Eventos') }}</a>
                
                
            </div>
            </div>
            <!-- /#sidebar-wrapper -->

            <!-- Page Content -->
            <div id="page-content-wrapper">

                <nav class="navbar navbar-expand-lg navbar-light">
                    <button class="btn btn-info" id="menu-toggle"><i class="fas fa-bars"></i></button>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{ Auth::user()->name }} </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <a class="text-danger dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar sesión <i class="fas fa-sign-out-alt"></i>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: one;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                        <li class="nav-item">

                        </li>
                    </ul>
                    </div>
                </nav>

                <div class="container-fluid my-4 px-4 bg-repeat" style="min-height: 100%">
                    
                    @yield('content')
                </div>
            </div>
            <!-- /#page-content-wrapper -->
        </div>
        <!-- /#wrapper -->

        <!-- Menu Toggle Script -->
        <script>
            $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            });
        </script>
    </body>
    @yield('datatable')
</html>
