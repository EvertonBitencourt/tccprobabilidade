<?php
define('BOT_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
define('VERIFY_TOKEN', 'EAAFOUAO7YbIBAKASvfrswSYPNDlyQuaq0W2DnPH0wEJT477WObls1nyrKbXkEEZBIG8xYvP4FxJzvv3ZCXpKfZCDpW0eEQhi94ijGSKlvhgGtIqYjZAKaqDZAu4407bfq2pZAQDaACymHxv0XZAVMdzvVLZAKrnMC3HGRuFtB34wMCgaLsD9eEzr');
$hub_verify_token = null;
//-----VEFICA O WEBHOOK-----//
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === VERIFY_TOKEN) {
    echo $challenge;
}
//-----FIM VERIFICAÇÃO-----//
?>