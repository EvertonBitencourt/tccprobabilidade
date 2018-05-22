<?php

/*setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );
date_default_timezone_set( 'America/Sao_Paulo' );*/

//https://developers.facebook.com/docs/messenger-platform/webhook-reference/message-received


define('TESTE', false);
define('BOT_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
define('VERIFY_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
define('API_URL', 'https://graph.facebook.com/v2.6/me/messages?access_token=' . BOT_TOKEN);
$hub_verify_token = null;


require 'funcoes_bot.php';

//-----VEFICA O WEBHOOK-----//
if (isset($_REQUEST['hub_challenge'])) {
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


?>


