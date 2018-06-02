<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>APPROBABILIDADE: EXERCICIOS PRONTOS</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/album/">
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="album.css" rel="stylesheet">
</head>

<body>
<div class="collapse bg-inverse" id="navbarHeader">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 py-4">
                <h4 class="text-white">Quem somos</h4>
                <p class="text-muted">Página na web do Learaar</p>
            </div>
            <div class="col-sm-4 py-4">
                <h4 class="text-white">Fale comigo</h4>
                <ul class="list-unstyled">
                    <li><a href="https://www.facebook.com/learaar/" class="text-muted">Diga Oi</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="navbar navbar-inverse bg-inverse">
    <div class="container d-flex justify-content-between">
        <a href="#" class="navbar-brand">Learaar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</div>

<div class="jumbotron">
    <div class="card-block">
        <h1>Exercicios resolvidos</h1>
        <p class="card-text text-justify">Bem vindo a página de exercicios prontos do Learaar, aqui você irá visualizar exercícios prontos das categorias abaixo, clique nos botões
            para exibir os problemas de cada categoria, após clique nos botões para visualizar as soluções com um passo a passo.</p>
    </div>
    <br>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills mine">
                    <!-- <li class="nav-item"><a class="nav-link" href="#dependents" data-toggle="tab">Dependents</a></li> -->
                    <li id="probabilidade1" class="nav-item"><a class="<?php if($_GET['texto'] != 'e')echo 'active'; echo ' nav-link' ?>" href="#prob" data-toggle="tab">Probabilidade</a></li>
                    <li class="nav-item"><a id="teste" class="<?php if($_GET['texto'] == 'e')echo 'active'; echo ' nav-link' ?>" href="#espacoamostral" data-toggle="tab">Espaço Amostral</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="<?php if($_GET['texto'] != 'e')echo 'active'; echo ' tab-pane' ?>" id="prob">
                        <!-- The timeline -->
                        <div id="probabilidade" class="card-header">
                            <h2>Questão 1</h2>
                            <p class="card-text">Em uma gaveta temos 12 camisas, das quais, quatro são de gola polo e o restante, de gola normal.
                                Retirando duas camisas sucessivamente ao acaso e sem reposição, qual é a probabilidade de as  duas camisas serem de gola polo?</p>
                            <div class="btn btn-primary" onclick="esconder('solucao1')">
                                <h6>Solução 1</h6></div>
                            <p id="solucao1" class="card-text" style="display: none">Camisas gola normal: 8 em 12.<br>
                                Camisas gola polo: 4 em 12.<br>
                                Retirando camisas gola polo sucessivamente, sem reposição:<br>
                                1º retirada gola polo = 4 em 12.<br>
                                2º retirada considerando a 1º gola polo = 3 em 11.<br>
                                4/12 * 3/11 = 12/132 = 0,0909 = 9,09%.<br>
                                A probabilidade é de 9,09%.<br></p>
                            <h2>Questão 2</h2>
                            <p class="card-text">Qual é a probabilidade de, no lançamento de 4 moedas, obtermos cara em todos os resultados?</p>
                            <div class="btn btn-primary" onclick="esconder('solucao2')">
                                <h6>Solução 2</h6>
                            </div>
                            <p id="solucao2" class="card-text" style="display: none">Primeiramente, é necessário encontrar o número total de possibilidades de resultados:<br>
                                2·2·2·2 = 16<br>
                                Posteriormente, devemos encontrar o número de possibilidades de obter cara em todos os resultados. Na realidade, só existe uma possibilidade de que isso aconteça.<br>
                                Por fim, basta dividir o segundo pelo primeiro:<br>
                                1 = 0,0625<br>
                                16<br>
                                Multiplicando 6,25 por 100, para obter um percentual, teremos: 6,25%<br></p>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php if($_GET['texto'] == 'e')echo 'active'; echo ' tab-pane' ?>" id="espacoamostral">
                        <!-- The timeline -->
                        <div id="ep" class="card-header">
                            <h2>Questão 1</h2>
                            <p class="card-text">Lance 2 dados, diga qual o espaço amostral.</p>
                            <div class="btn btn-primary" onclick="esconder('epsolucao1')">
                                <h6>Solução 1</h6></div>
                            <p id="epsolucao1" class="card-text" style="display: none">Para obter o espaço amostral é levado em consideração o número de faces do objeto,
                                portanto como estamos usando dois dados o calculo é 6 faces do 1º dado, multiplicado por 6 faces do 2º objeto, logo para obter o espaço amostral o calculo é:<br>
                                6 * 6 = 36<br>
                                O espaço amostral é 36.<br></p>
                            <h2>Questão 2</h2>
                            <p class="card-text">Qual é o espaço amostral no lançamento de 4 moedas?</p>
                            <div class="btn btn-primary" onclick="esconder('epsolucao2')">
                                <h6>Solução 2</h6>
                            </div>
                            <p id="epsolucao2" class="card-text" style="display: none">Para obter o espaço amostral é levado em consideração o número de faces do objeto,
                                portanto como estamos usando 4 moedas o calculo é 2 faces da 1º moeda, multiplicado por 2 faces do 2º objeto, multiplicado por 2 faces do 3º objeto, multiplicado por 2 faces do 4º objeto, logo para obter o espaço amostral o calculo é:<br>
                                2 * 2 * 2 * 2= 16<br>
                                O espaço amostral é 16.<br></p>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="interests">

                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>

<footer class="footer navbar navbar-inverse bg-inverse">
    <div class="text-muted">
        <a class="float-right" href="#">Back to top</a>
    </div>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="../../assets/js/vendor/holder.min.js"></script>
<script>
    $(function () {
        Holder.addTheme("thumb", { background: "#55595c", foreground: "#eceeef", text: "Thumbnail" });
    });
    function esconder (id){
        var display = document.getElementById(id).style.display;
        if(display == "none")
            document.getElementById(id).style.display = 'block';
        else
            document.getElementById(id).style.display = 'none';
    };
</script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
