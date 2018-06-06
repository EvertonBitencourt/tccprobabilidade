<?php
/**
 * Created by PhpStorm.
 * User: Everton
 * Date: 21/05/2018
 * Time: 10:00
 */

require 'db.class.php';
require 'dialogo.php';
function processMessage($message) {
    // processa a mensagem recebida
    $results = print_r($message, true);
    file_put_contents("log.txt", $results . PHP_EOL, FILE_APPEND);
    $sender = $message['sender']['id'];
    $recipient = $message['recipient']['id'];
    $text = $message['message']['text'];//texto recebido na mensagem
    if (isset($text)) {
        salvar_mensagem($sender, $text, $recipient);

        if($sender!=305572973182638) {
            $caso = verificar_usuario_bd($sender);

            if ($caso == 1) {
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Olá! Eu sou o professor Learaar que vai lhe ajudar a estudar e se preparar para solucionar problemas de probabilidade! Para começar preciso saber seu nome?')));
            }
            if ($caso == 2) {
                atualizar_nome($text, $sender);
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'O que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.\nDigite o número da opção.')));
            }
            if ($caso == 3) {
                $resposta = dialogo($sender, $text);
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => $resposta)));
                // usar para consultar select to_char(data_hora, 'dd/mm/yyyy hh24:mi') from historico;
            }
        }
    }/*else{
     /*   sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Ainda não reconheço imagens.')));
    }*/
}

function verificar_palavra($mensagem,$termo){
    $sim = array('sim', 'afirmativo', 'claro', 'afirmativo', 'certo', 'ok', 'yeah');
    $nao = array('não', 'jamais', 'recuso', 'nunca', 'nops');
    $cancelar = array('cancelar', 'parar','stop','sair','encerrar');
    foreach ($$termo as $k) {
        if (ctexto($mensagem, $k, 2)) {
            return true;
        }
        return false;
    }
}

function abrir_banco(){
    return new DB( 'lzrymjxrdqcmhe', //usuario
        'a0a6acc595e5c2591749b76679342e03b140dc8b81c1a6e757b5feba58b3e665',//senha
        'd8ji7jlpf7b7rq', //banco
        'ec2-50-16-204-127.compute-1.amazonaws.com'//servidor
    );
}

/*function historico($valor){
    $db = abrir_banco();
    $historico = $db->query("SELECT * FROM historico ORDER BY data_hora DESC;")->fetchAll(PDO::FETCH_ASSOC);
    $conversa ="";
    while($valor > 0){
        $conversa = $conversa.$historico[$valor]['mensagem']."<br>";
        $valor --;
    }
    return $conversa;
}*/

/*
function consulta_msg($id, $quantidade){
    $db = abrir_banco();
    $msg = $db->query("SELECT * FROM historico WHERE id_origem = $id OR id_destino = $id ORDER BY data_hora DESC;")->fetchAll(PDO::FETCH_ASSOC);
    $usuario = $db->query("SELECT nome FROM usuario WHERE id_usuario = $id")->fetchAll(PDO::FETCH_ASSOC);
    $usuario = "everton";
    $retorno = "";
    foreach($msg as $line){
        $retorno = $retorno.$line["data_hora"]." - ".$usuario.": ".$line["mensagem"]."\n";
    }
    return $retorno;
}*/

function ctexto($mensagem, $termo, $margem){ // comparar textos
    $mensagem = strtolower($mensagem);
    $termo = strtolower($termo);
    $comp = levenshtein($mensagem,$termo);
    $margem = $margem;
    if($comp<=$margem) {
        return true;
    }else
        return false;
}

function verificarCategoria($texto,$idusuario){
    $db = abrir_banco();
    $categoria = $db->query("SELECT * FROM categoria") ->fetchAll(PDO::FETCH_ASSOC);
    $cat_lev = 0;
    foreach ($categoria as $value){
        if(ctexto($texto, $value["nome"], 3)){
            $cat_lev = $value["id_categoria"];
            $db->query("insert into problema (id_usuario,id_categoria, data_hora) values ($idusuario,$cat_lev,current_timestamp)");
        }
    }
    return $cat_lev;
}

function ultimoproblema($id_usuario){ //encontrar o último problema, para fornecer informações para o metodo resolver
    $db = abrir_banco();
    $id = $db->query("SELECT id_problema FROM problema where id_usuario=$id_usuario order by data_hora desc")->fetchAll(PDO::FETCH_ASSOC);
    return $id[0]["id_problema"]; //$id[0]=>["id_problema"]
}

function verificarObjeto($message,$id_usuario){ // implementar no dialogo a troca do if por este método
    $db = abrir_banco();
    $objeto = $db->query("SELECT * FROM objeto") ->fetchAll(PDO::FETCH_ASSOC);
    $id = ultimoproblema($id_usuario);
    $ob = false;
    foreach ($objeto as $value){
        if(ctexto($message,$value["nome"], 3)){
            $ob = $value["id_objeto"];
            definirdado($id,2,$ob);
            $ob = $value["nome"];
        }
    }
    return $ob;
}

function obterdado($id_problema, $id){
    $db = abrir_banco();
    $valor = $db->query("select valor from dado where id_problema = $id_problema and id = $id")->fetch()['valor'];
    return $valor;
}

function obterdados($id_problema){
    $db = abrir_banco();
    $dados = $db->query("SELECT valor FROM dado WHERE id_problema = $id_problema ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    return $dados;
}

function definirdado($id_problema, $id, $valor){
    $db = abrir_banco();
    $db->query("insert into dado (id_problema, id, valor) values ($id_problema,$id,'$valor')");
}

function atualizardado($id_problema,$id,$valor){
    $db = abrir_banco();
    $db->query("update dado set valor = '$valor' where id_problema=$id_problema and id=$id");
}

function resolver($id_usuario, $detalhado){
    $db = abrir_banco();
    $id_problema = ultimoproblema($id_usuario);
    $problema = $db->query("SELECT * FROM problema where id_problema = $id_problema")->fetch();
    $categoria_problema = $problema["id_categoria"];
    if($categoria_problema == 1){

    }
    if($categoria_problema == 2){
        $dado1 = obterdado($id_problema,1);
        if(ctexto($dado1,"retirar",3)){
            $solucao = calcular_espaco_amostral_retirar($id_problema,$detalhado);
        }
        if(ctexto($dado1,"lançar",3)){
            $faces = $db->query("SELECT faces FROM objeto where id_objeto = $dado1")->fetch()["faces"];
            $solucao = calcular_espaco_amostral(obterdado($id_problema,3), $faces, $detalhado);
        }
    }
    if($categoria_problema == 3){
        $dado1 = obterdado($id_problema,1);
        if(ctexto($dado1,"lançar",3)) {
            $dado2 = obterdado($id_problema,2);
            $faces = $db->query("SELECT faces FROM objeto where id_objeto = $dado2")->fetch()["faces"];
            $solucao = calcular_probabilidade(obterdado($id_problema, 3), $faces, obterdado($id_problema, 4), $detalhado);
        }
        if(ctexto($dado1,"retirar",3)){
            $consulta = obterdado($id_problema,2)*2+3;
            $qretiradas =obterdado($id_problema,$consulta);
            $independe = obterdado($id_problema,$qretiradas+$consulta+1);
            if(ctexto($independe,"true",2)){
                $solucao = calcular_probabilidade_retirada_independente($id_problema,$detalhado);
            }else /*($independe == "false")*/{
                $solucao = calcular_probabilidade_retirada_dependente($id_problema,$detalhado);
            }
        }
    }
    return $solucao;
}

function calcular_espaco_amostral_retirar($id_problema,$detalhado)
{
    $dados = obterdados($id_problema);
    $consulta = obterdado($id_problema, 2) * 2 + 3;
    $qretiradas = obterdado($id_problema, $consulta);
    $independe = obterdado($id_problema, $qretiradas + $consulta + 1);
    if (ctexto($independe, "true", 2)) {
        $total = 0;
            $texto = "O espaço amostral neste caso é a multiplicação do total objetos a cada retirada, logo ";
            $consulta = $dados[1]['valor'];
            while ($consulta > 0) {
                $total += $dados[$consulta * 2 + 1]['valor'];
                $texto = $texto . $dados[$consulta * 2 + 1]['valor'];
                if ($consulta != 1) {
                    $texto = $texto . " + ";
                } else {
                    $texto = $texto . " = " . $total . "\n";
                }
                $consulta--;
            }
            $qretiradas = $dados[$dados[1]['valor'] * 2 + 2]['valor'];
            $qretiradas--;
            $temp = $total;
            while ($qretiradas > 0) {
                $total *= $temp;
                $texto = $texto . $temp;
                if ($qretiradas != 1) {
                    $texto = $texto . " * ";
                } else {
                    $texto = $texto . " * " . $temp . " = " . $total . "\n";
                }
                $qretiradas--;
            }
        }
        else {
        $total = 0;
            $texto = "O espaço amostral neste caso é a multiplicação do total objetos a cada retirada, logo ";
            $dados = obterdados($id_problema);
            $consulta = $dados[1]['valor'];
            while ($consulta > 0) {
                $total += $dados[$consulta * 2 + 1]['valor'];
                $texto = $texto . $dados[$consulta * 2 + 1]['valor'];
                if ($consulta != 1) {
                    $texto = $texto . " + ";
                } else {
                    $texto = $texto . " = " . $total . "\n";
                }
                $consulta--;
            }
            $qretiradas = $dados[$dados[1]['valor'] * 2 + 2]['valor'];
            $qretiradas--;
            $temp = $total;
            while ($qretiradas > 0) {
                $texto = $texto . $temp;
                $temp --;
                $total *= $temp;
                if ($qretiradas != 1) {
                    $texto = $texto . " * ";
                } else {
                    $texto = $texto . " * " . $temp . " = " . $total . "\n";
                }
                $qretiradas--;
            }
    }
    if($detalhado){
        return $texto;
    }else { return $total;}
}

function calcular_probabilidade_retirada_independente($id_problema,$detalhado){
    $texto = "";
    $dados = obterdados($id_problema);
    $consulta = $dados[1]['valor']*2+2;
    $qretiradas = $dados[$consulta]['valor'];
    $total = 0;
    $limite = $dados[1]['valor'] * 2 + 3;
    $count = 3;
    while($count < $limite){
        $total += $dados[$count]['valor'];
        $count+=2;
    }
    $count=2;
    $qobjetos = $dados[1]['valor'];
    while($qobjetos> 0){
        $texto = $texto."A quantidade de ".$dados[$count]['valor']." é ".$dados[$count+1]['valor']." em ".$total.".\n";
        $count +=2;
        $qobjetos --;
        // A quantidade de camisa polo é 4 em 12.
        // A quantidade de camisa normal é 8 em 12.
    }

    while($qretiradas>0){
        $aretirar = $dados[$consulta+1]['valor'];
        $texto = $texto."Retiramos ".$dados[$aretirar-1]['valor']." = ".$dados[$aretirar]['valor']." em ".$total.".\n";
        $qretiradas --;
        $consulta ++;
    }
    $consulta = $dados[1]['valor']*2+2;
    $qretiradas = $dados[$consulta]['valor'];
    $count = $dados[1]['valor'] * 2 + 3;
    $aretirar = $dados[$count]['valor'];
    $texto = $texto.$dados[$aretirar]['valor']."/".$total." ";
    $v1 = $dados[$aretirar]['valor'];
    $v2 = $total;
    $probabilidade = $dados[$aretirar]['valor'] / $total;
    $qretiradas--;
    $count++;
    while ($qretiradas > 0 ) {
        $aretirar = $dados[$count]['valor'];
        $atual = $dados[$aretirar]['valor'] / $total;
        $texto = $texto."* ".$dados[$aretirar]['valor']."/".$total." = ".$v1*$dados[$aretirar]['valor']."/".$v2*$total." ";
        $v1 = $v1 * $dados[$aretirar]['valor'];
        $v2 = $v2 * $total;
        $probabilidade = $probabilidade * $atual;
        $qretiradas--;
        $count++;
    }
    $texto = $texto."= ".$probabilidade."\n";
    $probabilidade = round($probabilidade*100,2)."%";

    if($detalhado){
        $probabilidade = $texto.$probabilidade;
    }
    return $probabilidade;
}

function calcular_probabilidade_retirada_dependente($id_problema,$detalhado){
    $texto = "";
    $dados = obterdados($id_problema);
    $consulta = $dados[1]['valor']*2+2;
    $qretiradas = $dados[$consulta]['valor'];
    $total = 0;
    $limite = $dados[1]['valor'] * 2 + 3;
    $count = 3;
    while($count < $limite){
        $total += $dados[$count]['valor'];
        $count+=2;
    }
    $count=2;
    $qobjetos = $dados[1]['valor'];
    while($qobjetos> 0){
        $texto = $texto."A quantidade de ".$dados[$count]['valor']." é ".$dados[$count+1]['valor']." em ".$total.".\n";
        $count +=2;
        $qobjetos --;
    }
    $temp = $total;
    $dtemp = $dados;
    while($qretiradas>0){
        $aretirar = $dtemp[$consulta+1]['valor'];
        $texto = $texto."Retiramos ".$dtemp[$aretirar-1]['valor']." = ".$dtemp[$aretirar]['valor']." em ".$temp.".\n";
        $novo = $dtemp[$aretirar]['valor'] - 1 ;
        $dtemp[$aretirar]['valor'] = $novo;
        $qretiradas --;
        $consulta ++;
        $temp--;
    }
    $consulta = $dados[1]['valor']*2+2;
    $qretiradas = $dados[$consulta]['valor'];
    $count = $dados[1]['valor'] * 2 + 3;
    $aretirar = $dados[$count]['valor'];
    $texto = $texto.$dados[$aretirar]['valor']."/".$total." ";
    $v1 = $dados[$aretirar]['valor'];
    $v2 = $total;
    $probabilidade = $dados[$aretirar]['valor'] / $total;
    $novo = $dados[$aretirar]['valor'] - 1 ;
    $dados[$aretirar]['valor'] = $novo;
    $qretiradas--;
    $count++;
    $total--;
    while ($qretiradas > 0 ) {
        $aretirar = $dados[$count]['valor'];
        $atual = $dados[$aretirar]['valor'] / $total;
        $texto = $texto."* ".$dados[$aretirar]['valor']."/".$total." = ".$v1*$dados[$aretirar]['valor']."/".$v2*$total." ";
        $v1 = $v1 * $dados[$aretirar]['valor'];
        $v2 = $v2 * $total;
        $probabilidade = $probabilidade * $atual;
        $novo = $dados[$aretirar]['valor'] - 1 ;
        $dados[$aretirar]['valor'] = $novo;
        $qretiradas--;
        $count++;
        $total--;
    }
    $texto = $texto."= ".$probabilidade."\n";
    $probabilidade = round($probabilidade*100,2)."%";

    if($detalhado){
        $probabilidade = $texto.$probabilidade;
    }
    return $probabilidade;
}

function identificarobjeto($id_problema,$objeto){
    $limite = obterdado($id_problema,2)*2+2;
    $contador = 3;
    $posicao = false;
    while($contador <=$limite){
        if(ctexto($objeto, obterdado($id_problema,$contador),3)){
            $posicao = $contador;
            break;
        }else{$contador +=2;}
    }
    return $posicao;
}

function calcular_espaco_amostral($lancamentos, $faces, $detalhado){
    if($detalhado){
        $explicacao = "Espaço amostral é o conjunto estabelecido por todos os possíveis resultados de um experimento. Logo devemos elevar a quantidade de possibilidades (";
        $explicacao = $explicacao.$faces.") à quantidade de lançamentos (".$lancamentos."). Portanto o resultado é: ".pow($faces,$lancamentos);
        return $explicacao;
    }else
        return pow($faces,$lancamentos);
}

function calcular_probabilidade($lancamento, $faces, $eventos, $detalhado){
    $espaco = pow($faces, $lancamento);//2^2= 4
    $espaco_eventos = pow($eventos,$lancamento);
    $probabilidade = round(100/$espaco*$espaco_eventos,2)." %";
    $texto = "A probabilidade é calculada da seguinte forma:\n";
    // e então multiplicaremos este resultado pelo espaço amostral dos resultados aceitaveis.
    $texto = $texto."Dividiremos uma centena pelo espaço amostral(quantidade de resultados possiveis, obtido elevando a quantidade de faces do objeto a quantidade de lançamentos) do problema (".$espaco.").\n";
    $texto = $texto."Multiplicaremos o valor obtido pelo espaço amostral dos resultados desejados (".$espaco_eventos.").\n";
    $texto = $texto."Sendo assim temos: 100 / ".$espaco." * ".$espaco_eventos." = ";
    if($detalhado){
        $probabilidade = $texto.$probabilidade;
    }
    return $probabilidade;
}

function obter_categoria($id_problema){
    $db = abrir_banco();
    return $db->query("select id_categoria from problema where id_problema = $id_problema")->fetch()["id_categoria"];
}

function salvar_mensagem($idorigem, $mensagem, $idest){
    $db = abrir_banco();
    $db->query("INSERT INTO historico (id_origem, mensagem, data_hora, id_destino) VALUES ($idorigem, '$mensagem', current_timestamp, $idest )");

}

function verificar_usuario_bd($id){
    $db = abrir_banco();
    $consulta = $db->query("SELECT * FROM usuario where id_usuario = $id")->fetchAll(); //fetchall retorna o resultado da query
    if (count($consulta) == 0) { //comparar o resultado para verificar se ja esta no banco
        $db->query("INSERT INTO usuario (id_usuario,nome, etapa) VALUES($id, 'oi', 1)"); // senão sestiver no banco insere com nome padrão
        return 1;
    }
    $consulta = $db->query("SELECT * FROM usuario where id_usuario = $id and nome = 'oi'")->fetchAll();
    if(count($consulta) == 1){ //verificar a existencia do usuário no banco com nome padrão
        return 2; // retorno para chamar o caso 2 na função processmessage
    }else return 3;
}

function atualizar_nome($message,$sender){
    $db = abrir_banco();
    $db->query("UPDATE usuario set nome='$message' where id_usuario =$sender");// atualizando o nome com o texto da mensagem do usuário
}

function atualizar_etapa($id, $etapa){
    $db = abrir_banco();
    $db ->query("UPDATE usuario SET etapa = $etapa where id_usuario = $id");

}

function debug($mensagem){
    file_put_contents('debug.txt',$mensagem);
}

function sendMessage($parameters)
{
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => json_encode($parameters),
            'header' => "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create( $options );

    if(TESTE){
        print_r($parameters);
        exit();
    }
    file_get_contents(API_URL, false, $context );
}