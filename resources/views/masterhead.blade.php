<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title>Módulos boletos</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">


</head>
 <!--HEADER-->
 <header class="mb-5">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light justify-content-between">
            <a class="navbar-brand" href="#">
                <img src="" class="img-fluid logo" />
            </a>

            <div class="collapse navbar-collapse menu-list">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " href="{{route('remessa')}}"><i class="bi bi-file-earmark-arrow-up"></i> Remessa </a>

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('retorno')}}"><i class="bi bi-file-earmark-arrow-down"></i> Retorno</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('massaView')}}"><i class="bi bi-receipt-cutoff"></i>Boletos em Massa </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('buscarBoleto')}}"><i class="bi bi-search"></i> buscar boleto</a>
                    </li>




                </ul>
            </div>
{{--
            <div class="collapse navbar-collapse flex-row-reverse" id="navbarNav">
                <ul class="navbar-nav itens-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-search"></i></a>

                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight" href="#"><i class="bi bi-cart"></i></a>
                    </li> -->


                </ul>
            </div> --}}
        </nav>
    </div>
</header>
<!--//HEADER-->
<body class="antialiased">
    <div class="container ">
