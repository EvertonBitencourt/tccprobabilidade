<?php
/**
 * Created by PhpStorm.
 * User: Everton
 * Date: 21/05/2018
 * Time: 10:00
 */
require 'db.class.php';
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
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Olá! Eu sou o professor Learaar que vai lhe ajudar a solucionar os seus problemas de probabilidade! Para começar preciso saber seu nome?')));
            }
            if ($caso == 2) {
                atualizar_nome($text, $sender);//criar ainda
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Você sabe qual a categoria de seu problema ?')));
            }
            if ($caso == 3) {
                $resposta = dialogo($sender, $text);
                //sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' =>consulta_msg(1406614996122042,20))));
                sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' =>$resposta)));
                //verificarCategoria($text);

                // usar para consultar select to_char(data_hora, 'dd/mm/yyyy hh24:mi') from historico;
            }
        }
    }/*else{
     /*   sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Ainda não reconheço imagens.')));
    }*/
}

function abrir_banco(){
    return new DB( 'lzrymjxrdqcmhe', //usuario
        'a0a6acc595e5c2591749b76679342e03b140dc8b81c1a6e757b5feba58b3e665',//senha
        'd8ji7jlpf7b7rq', //banco
        'ec2-50-16-204-127.compute-1.amazonaws.com'//servidor
    );
}
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
function ultimoproblema($id_usuario){
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
            $db->query("update problema set dado1=$ob where id_problema = $id");
            $ob = $value["nome"];
        }
    }
    debug($ob);
    return $ob;
}

function resolver($id_usuario, $detalhado){
    $db = abrir_banco();
    $id_problema = ultimoproblema($id_usuario);
    $problema = $db->query("SELECT * FROM problema where id_problema = $id_problema")->fetch();
    $dado1 = $problema["dado1"];
    $dado2 = $problema["dado2"];
    $dado3 = $problema["dado3"];
    $dado4 = $problema["dado4"];
    $categoria_problema = $problema["id_categoria"];
    if($categoria_problema == 1){

    }
    if($categoria_problema == 2){
        $faces = $db->query("SELECT faces FROM objeto where id_objeto = $dado1")->fetch()["faces"];
        $solucao = calcular_espaco_amostral($dado2, $faces, $detalhado);
    }
    if($categoria_problema == 3){

    }
    return $solucao;
}

function calcular_espaco_amostral($lancamentos, $faces, $detalhado){
    if($detalhado){
        $explicacao = "Espaço amostral é o conjunto estabelecido por todos os possíveis resultados de um experimento. Logo devemos elevar a quantidade de possibilidades (";
        $explicacao = $explicacao.$faces.") à quantidade de lançamentos (".$lancamentos."). Portanto o resultado é: ".pow($faces,$lancamentos);
        return $explicacao;
    }else
        return pow($faces,$lancamentos);
}

function calcular_probabilidade($lancamento, $faces, $detalhado){
    $espaco = pow($faces, $lancamento);//2^2= 4
    $probabilidade = round(100/$espaco,2)." %";
    if($detalhado){
        $probabilidade = "A Probabilidade é calculada da seguinte forma(Completar a forma), portanto o resultado é:".$probabilidade;
    }
    return $probabilidade;
}

function dialogo($id, $mensagem){

    $resposta = "não entendi sua mensagem, por favor digite novamente, verificando sua ortografia, eu sou sensivel :( ";
    $db = abrir_banco();

    $consulta_etapa = $db ->query("SELECT etapa from usuario where id_usuario = $id")->fetch();
    $etapa = $consulta_etapa[0];
    if($etapa == 1){
        if(ctexto($mensagem,"sim", 2)){
            atualizar_etapa($id, 2);
            $resposta ="Qual é a categoria?"; //teste
        }
        if(ctexto($mensagem,"não", 2)){
            $resposta ="Veja o material que seu professor compartilhou com você em sala de aula ou consulte: https://brasilescola.uol.com.br/matematica/probabilidade.htm \n Após a consulta informe a categoria de seu problema entre as categorias abaixo: \n Espaço Amostral \n Arranjos e Permutações"; //teste
            atualizar_etapa($id, 2);
        }
    }
    if($etapa == 2){
        $categoria = verificarCategoria($mensagem,$id);
        if($categoria == 2 || $categoria == 3){
            $resposta = "Para lhe ajudar melhor preciso saber algumas informações de seu problema, favor responda claramente os próximos questionamentos. Qual objeto está usando?";
            atualizar_etapa($id, 3);
        }
    }
    if($etapa == 3){
        $objeto = verificarObjeto($mensagem,$id);
        if($objeto != false){//atualizar diagram de fluxo de dados com esse item
            $resposta = "Quantas vezes você irá lançar o(a) ".$objeto;
            atualizar_etapa($id, 3.1);
        }
    }
    if($etapa == 3.1){
        if($mensagem >0){
            $problema = ultimoproblema($id);
            $db->query("update problema set dado2=$mensagem where id_problema = $problema");
            $resposta = "Você deseja ter uma resposta detalhada? Responda com sim ou não.";
            atualizar_etapa($id, 3.2);
        }
    }
    if($etapa == 3.2){
        if(ctexto($mensagem,"não",2)){
            $resposta = resolver($id, false);
            $resposta = $resposta."\n Deseja resolver outro problema?";
            atualizar_etapa($id, 4);
        }
        if(ctexto($mensagem,"sim",2)){
            $resposta = resolver($id, true);
            $resposta = $resposta."\n Deseja resolver outro problema?";
            atualizar_etapa($id, 4);
        }
    }
    if($etapa == 4){
        if(ctexto($mensagem, "sim", 2)){
            atualizar_etapa($id, 1);
            $resposta ="Você sabe qual é a categoria?";
        }
        if(ctexto($mensagem, "não", 2)){
            atualizar_etapa($id, 5);
            $resposta = "Vá conversar com a siri então.";
        }
    }
    if($etapa == 5){
        atualizar_etapa($id,1);
        $resposta = "Veja só quem voltou?!?!\nSó me procura quando tens problemas, não é mesmo? \n Você sabe qual a categoria do seu problema?";
    }
    /*if($etapa == 6){
        $objeto = verificarObjeto($mensagem);
        if(!ctexto($objeto,"vazio", 1)){//atualizar diagram de fluxo de dados com esse item
            $resposta = "Quantas vezes o evento deve acontecer?";
            atualizar_etapa($id, 6.1);
        }
    }
    if($etapa ==6.1){
        if($mensagem >0) {
            $resposta = "Quantas vezes o evento deve acontecer?";
            atualizar_etapa($id,6.2);
        }
    }
    if(etapa == 6.2){
        if($mensagem >0) {
            $resposta = "Quantas vezes lançará o objeto?";
            atualizar_etapa($id,6.3);
        }
    }
    if(etapa == 6.3){
        if($mensagem >0) {
            $resposta = "Você deseja ter uma resposta detalhada? Responda com sim ou não.";
            atualizar_etapa($id, 6.4);
        }
    }
    if(etapa == 6.4){
        $consulta = $db->query("select mensagem from historico where id_origem=$id order by data_hora")->fetchAll(PDO::FETCH_ASSOC);

        if(ctexto($mensagem,"não",2)){
            $resposta = calcular_probabilidade($evento,)
        }
    }*/
    /* if($mensagem == verificarCategoria($mensagem)) {

     }*/
    return $resposta;
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