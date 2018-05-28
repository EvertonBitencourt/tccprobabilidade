<pre>
<?php
/**
 * Created by PhpStorm.
 * User: Everton
 * Date: 21/05/2018
 * Time: 09:58
 */
define('TESTE', true);
include 'funcoes_bot.php';
$mensagem = '{
    "object":"page",
  "entry":
    [{
    "id":"320753758271687",
      "time":1471957860368,
      "messaging":
        [{
        "sender":{
            "id":"8"
          },
          "recipient":{
            "id":"305572973182638"
          },
          "timestamp":1471957860318,
          "message":{
            "mid":"mid.1471957860205:70331a194f8c8af354",
            "seq":24,
            "text":"'.$_GET['mensagem'].'"
          }
        }]
    }]
}';
$mensagem = json_decode($mensagem, true);

$update  = $mensagem['entry'][0]['messaging'][0];

processMessage($update);