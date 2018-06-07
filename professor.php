<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>APPROBABILIDADE: AREA DO PROFESSOR</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/album/">
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <?php
    require 'funcoes_bot.php';
    ?>
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
            <h1>Escolha o que deseja fazer</h1>
            <p class="card-text text-justify">Nesta area você pode escolher: Ver o histórico de suas turmas ou criar uma nova turma.</p>
            <a class="btn btn-primary" href="historico.php">Ver Histórico</a>
            <div id="cadastrar" class="btn btn-primary" onclick="esconder('cad')">Cadastrar Turma</div>
            <form id="cad"  role = "form" method="post" action="professor.php" style="display: none">
                <br><label for = "name"> Diga o nome da sua turma </label>
                    <input type ="text" name = "turmanova">
                    <input type ="submit" class="btn btn-primary">
            </form>
            <?php
            if(isset($_POST['turmanova'])){
                echo '<div id = "msg"> A turma '.$_POST['turmanova'].' foi salva </div>';
                inserir_turma($_POST['turmanova']);
            }
            ?>
        </div>
</div>

<footer id="footer" class="footer navbar navbar-inverse bg-inverse">
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

            document.getElementById('msg').style.display = 'none';
    };
</script>
<script>
    $(document).ready(function() {

        var docHeight = $(window).height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;
        $('body').css('background-color','#eceeef');
        if (footerTop < docHeight)
            $('#footer').css('margin-top', 10+ (docHeight - footerTop) + 'px');
    });
</script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>