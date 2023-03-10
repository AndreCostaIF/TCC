<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title>{{isset($title) ? $title . ' -' : ''}}  Módulo boletos</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon-intelnet.ico') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">


</head>
<!--HEADER-->
<header class="mb-5">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light justify-content-between">
            <div class="collapse navbar-collapse menu-list">
                <ul class="navbar-nav">

                    @if (session()->get('grupo_users_id') == 1)
                        <li class="nav-item">
                            <a class="nav-link " href="{{ route('remessa') }}"><i
                                    class="bi bi-file-earmark-arrow-up"></i> Remessa </a>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('retorno') }}"><i
                                    class="bi bi-file-earmark-arrow-down"></i> Retorno</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('massaView') }}"><i
                                    class="bi bi-receipt-cutoff"></i>Boletos em Massa </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('buscarBoleto') }}"><i class="bi bi-search"></i> buscar
                            boleto
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pix') }}">
                            <img src="{{ asset('assets/icone-pix.svg') }}" alt="" width="25" height="25"> área PIX
                        </a>
                    </li>
                </ul>
            </div>

            <div class="collapse aling-content-center navbar-collapse flex-row-reverse" id="navbarNav">
                <ul class="navbar-nav itens-menu align-items-center">
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link">{{ session()->get('nome') }} <i class="bi bi-caret-down"></i></a>
                            <div class="dropdown-content">

                                @if (session()->get('grupo_users_id') == 1)
                                    <a href="{{ route('remessa') }}"><i class="bi bi-file-earmark-arrow-up"></i>
                                        Remessa
                                    </a>
                                    <a href="{{ route('retorno') }}"><i class="bi bi-file-earmark-arrow-down"></i>
                                        Retorno
                                    </a>
                                @endif

                                <a href="{{ route('logout') }}"><i class="bi bi-box-arrow-right text-danger"></i>
                                    Sair</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!--//HEADER-->

<body class="antialiased">
    <div class="container ">
