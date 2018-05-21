<?php

/*setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );
date_default_timezone_set( 'America/Sao_Paulo' );*/

//https://developers.facebook.com/docs/messenger-platform/webhook-reference/message-received

require('parser.php');
require 'db.class.php';
define('BOT_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
define('VERIFY_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
define('API_URL', 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_TOKEN);
$hub_verify_token = null;

$os = array("dado", "cartas", "moeda", "moedas", "dados","carta");


function processMessage($message) {
  // processa a mensagem recebida
  $results = print_r($message, true);
  //file_put_contents("log.txt https://approbabilidade.herokuapp.com/log.php?dados=222". $results);
  file_put_contents("log.txt", $results . PHP_EOL, FILE_APPEND);
  $sender = $message['sender']['id'];
  $text = $message['message']['text'];//texto recebido na mensagem
  if (isset($text)) {

      //abrir_banco()->query("INSERT INTO historico (id_usuario, mensagem) VALUES ($sender, '$text')");
      salvar_mensagem($sender, $text);


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
            sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' =>$resposta)));
            //verificarCategoria($text);

            // usar para consultar select to_char(data_hora, 'dd/mm/yyyy hh24:mi') from historico;
        }
    }
  }
}

function abrir_banco(){
    return new DB( 'lzrymjxrdqcmhe', //usuario
        'a0a6acc595e5c2591749b76679342e03b140dc8b81c1a6e757b5feba58b3e665',//senha
        'd8ji7jlpf7b7rq', //banco
        'ec2-50-16-204-127.compute-1.amazonaws.com'//servidor
    );
}

function ctexto($mensagem, $termo, $margem){ // comparar textos
    $comp = levenshtein($mensagem,$termo);
    if($comp<=margem) {
        return true;
    }else
        return false;
}

function verificarCategoria($texto){
    $db = abrir_banco();
    $categoria = $db->query("SELECT nome FROM categoria") ->fetchAll(PDO::FETCH_ASSOC);
    $cat_lev = "sem categoria";
    foreach ($categoria as $value){
        if(ctexto($texto, $value, 2)){
            $cat_lev = $value;
        }
    }
    debug($cat_lev);
    return $cat_lev;
}

function calcular_espaco_amostral($lancamentos, $faces, $detalhado){
    if($detalhado){
        $explicacao = "Espaço amostral é o conjunto estabelecido por todos os possíveis resultados de um experimento. Logo devemos elevar a quantidade de lançamentos (";
        $explicacao = $explicacao.$lancamentos.") à quantidade de faces (".$faces."). Portanto o resultado é: ".pow($lancamentos,$faces);
        return $explicacao;
    }else
    return pow($lancamentos,$faces);
}
function dialogo($id, $mensagem){

    $resposta = "não entendi sua mensagem, por favor digite novamente, verificando sua ortografia, eu sou sensivel :( ";
    $db = abrir_banco();

    $consulta_etapa = $db ->query("SELECT etapa from usuario where id_usuario = $id")->fetch();
    $etapa = $consulta_etapa[0];
    if($etapa == 1){
        if(ctexto($mensagem,"sim", 1)){
            atualizar_etapa($id, 2);
            $resposta ="Qual é a categoria?"; //teste
        }
        if(ctexto($mensagem,"não", 1)){
            $resposta ="Veja o material que seu professor compartilhou com você em sala de aula ou consulte: https://brasilescola.uol.com.br/matematica/probabilidade.htm \n Após a consulta informe a categoria de seu problema entre as categorias abaixo: \n Espaço Amostral \n Arranjos e Permutações"; //teste
            atualizar_etapa($id, 2);
        }
    }
    if($etapa == 2){
        if(ctexto(verificarCategoria($mensagem),"Espaco Amostral",3)){
            $resposta = "Para lhe ajudar melhor preciso saber algumas informações de seu problema, favor responda claramente os próximos questionamentos. Qual objeto está usando?";
            atualizar_etapa($id, 3);
        }
    }
    if($etapa == 3){
        if(ctexto($mensagem,"dado", 1)){//atualizar diagram de fluxo de dados com esse item
            $resposta = "Quantas vezes você irá lançar este objeto";
            atualizar_etapa($id, 3.1);
        }
    }
    if($etapa == 3.1){
        if($mensagem >0){
            $resposta = "Você deseja ter uma resposta detalhada? Responda com sim ou não.";
            atualizar_etapa($id, 3.2);
        }
    }
    if($etapa == 3.2){
        $consulta = $db->query("select mensagem from historico where id_usuario=$id order by data_hora")->fetch();
        $valor = $consulta[count($consulta)-2];
        debug($valor." ".$mensagem);
        if(ctexto($mensagem,"não",1)){
            $resposta= calcular_espaco_amostral($valor[$mensagem],6, false);
        }else{
            $resposta=  calcular_espaco_amostral($valor,6, true);
        }
        $resposta = $resposta."\n Deseja resolver outro problema?";
        atualizar_etapa($id, 4);
    }
    if($etapa == 4){
        if(ctexto($mensagem, "sim", 1)){
                atualizar_etapa($id, 1);
                $resposta ="Qual é a categoria?";
            }
        if(ctexto($mensagem, "não", 1)){
            atualizar_etapa($id, 5);
            $resposta = "Vá conversar com a siri então.";
        }
    }
    if($etapa == 5){
        atualizar_etapa($id,1);
        $resposta = "Vejá quem voltou?!?!, Só me procura quando tens problemas, não é mesmo? \n Você sabe qual a categoria do seu problema?";
    }

   /* if($mensagem == verificarCategoria($mensagem)) {

    }*/
    return $resposta;
}

function salvar_mensagem($id, $mensagem){
    $db = abrir_banco();
    $db->query("INSERT INTO historico (id_usuario, mensagem, data_hora) VALUES ($id, '$mensagem', current_timestamp )");

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

function verificarObjeto($message){
    $consulta = $db->query("SELECT * FROM "); //teste de bosta
    /*$text = $message['message']['text'];
    if(in_array($text, $os)){
      sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Então, já sei o objeto que vamos trabalhar, quantas faces ou lado ele tem?')));
    }*/
}

function debug($mensagem){
    file_put_contents('debug.txt',$mensagem);
}

function sendMessage($parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$context  = stream_context_create( $options );
file_get_contents(API_URL, false, $context );
}
//-----VEFICA O WEBHOOK-----//
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === VERIFY_TOKEN) {
    echo $challenge;
}
//-----FIM VERIFICAÇÃO-----//
$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update['entry'][0]['messaging'][0])) {
  processMessage($update['entry'][0]['messaging'][0]);
} else {
   file_get_contents("https://approbabilidade.herokuapp.com/log.php?dados=Error");
}


//Voltar aqui para ver
/*if ($text === "Mega-Sena") {
      sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('megasena', $text))));
    }else if($text === "espaço amostral" || $text === "espaco amostral"){
      sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Agora que já sei que você deseja obter o Espaço Amostral, me diga qual o objeto que vamos usar?')));
    } else if($text === "moeda" || $text === "moedas" || $text === "dado" || $text === "dados" || $text === "carta" || $text === "cartas"){
      sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Então, já sei o objeto que vamos trabalhar, quantas faces ou lado ele tem?')));
    }  else*/

?>


