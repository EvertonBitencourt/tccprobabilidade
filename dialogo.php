<?php
/**
 * Created by PhpStorm.
 * User: Everton
 * Date: 26/05/2018
 * Time: 11:31
 */

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
            $resposta = "Para lhe ajudar melhor preciso saber algumas informações de seu problema, favor responda claramente os próximos questionamentos.\n Você irá retirar ou lançar algo?";
            atualizar_etapa($id, 2.5);
        }
        if($categoria == 1){

        }
    }
    if($etapa == 2.5){
        if(ctexto($mensagem, "lançar",3)){
            $problema = ultimoproblema($id);
            definirdado($problema,1,$mensagem);
            $resposta = "Qual objeto está usando?";
            atualizar_etapa($id, 3);
        }
        if(ctexto($mensagem, "retirar", 3)){
            $problema = ultimoproblema($id);
            definirdado($problema,1,$mensagem);
            atualizar_etapa($id, 2.6);
            $resposta = "Quantos objetos distintos você vai usar?";
        }
    }
    if($etapa == 2.6){
        if($mensagem >=2){
            $problema = ultimoproblema($id);
            definirdado($problema,2,$mensagem);
            $resposta = "Qual nome do 1º objeto?";
            atualizar_etapa($id, 2.7);
        }
        if($mensagem <2){
            $resposta = "Você precisar ter pelo menos dois objetos distintos!\nInforme uma nova quantidade de objetos";
        }
    }
    if($etapa == 2.7){
        $problema = ultimoproblema($id);
        $idado = 3;
        $flag = true;
        while ($flag){
            if(is_null(obterdado($problema,$idado))){
                definirdado($problema,$idado,$mensagem);
                $resposta = "Qual a quantidade de ".$mensagem." disponivel dentro do grupo que vamos retirar?";
                atualizar_etapa($id,2.8);
                $flag = false;
            }else $idado += 2;
        }
    }
    if($etapa == 2.8) {
        $problema = ultimoproblema($id);
        if ($mensagem>0) {
            $idado = 4;
            $flag = true;
            while ($flag) {
                if (is_null(obterdado($problema, $idado))) {
                    definirdado($problema, $idado, $mensagem);
                    if (obterdado($problema, 2)*2+2== $idado) {
                        $resposta = "Quantas retiradas irá fazer?";
                        atualizar_etapa($id, 2.9);
                    } else{
                        $resposta = "Qual nome do próximo objeto?";
                        atualizar_etapa($id, 2.7);
                    }
                    $flag = false;
                }else $idado += 2;
            }
        }
    }
    if($etapa == 2.9){
        if($mensagem>0){
            $problema = ultimoproblema($id);
            $consulta = obterdado($problema,2);
            $consulta = $consulta*2+3;
            definirdado($problema,$consulta,$mensagem);
            $resposta = "Que objeto você deseja retirar 1º?";
            atualizar_etapa($id,2.95);
        }
    }
    if($etapa == 2.95){
        $problema = ultimoproblema($id);
        $dados = obterdados($problema);
        $consulta = $dados[1]['valor'] * 2 + 2;
        $qretiradas = $dados[$consulta]['valor'];
        $idado = $consulta+1;
        $flag = true;

        while ($flag) {
            if($idado>30){$flag=false;}
            if (!isset($dados[$idado]['valor'])) {
                definirdado($problema, $idado + 1, identificarobjeto($problema,$mensagem)); //passando o nome do objeto a ser retirado
                if ($qretiradas == $idado - $consulta) {
                    $resposta = "São eventos independentes?";
                    atualizar_etapa($id, 2.96);
                } else{
                    $resposta = "Qual próximo objeto que deseja retirar?";
                }
                $flag = false;
            }else $idado++;
        }
    }
    if($etapa == 2.96){
        if(ctexto($mensagem,"não",2)){
            $problema = ultimoproblema($id);
            $consulta = obterdado($problema,2)*2+3;
            $qretiradas =obterdado($problema,$consulta);
            definirdado($problema,$qretiradas+$consulta+1,"false");
            $resposta = "Deseja uma resposta detalhada?";
            atualizar_etapa($id,3.2);
        }
        if(ctexto($mensagem,"sim",2)){
            $problema = ultimoproblema($id);
            $consulta = obterdado($problema,2)*2+3;
            $qretiradas =obterdado($problema,$consulta);
            definirdado($problema,$qretiradas+$consulta+1,"true");
            $resposta = "Deseja uma resposta detalhada?";
            atualizar_etapa($id,3.2);
        }
    }
    if($etapa == 3){
        $objeto = verificarObjeto($mensagem,$id);
        if($objeto != false){//atualizar diagram de fluxo de dados com esse item
            $resposta = "Quantas vezes você irá lançar o(a) ".$objeto;
            if(obter_categoria(ultimoproblema($id)) == 3) atualizar_etapa($id,3.07);
            else atualizar_etapa($id, 3.1);
        }
    }
    /*if($etapa == 3.05){ voltar AQUI PARA ARRUMAR O FLUXO
        if($mensagem >0){
            $problema = ultimoproblema($id);
            $db->query("update problema set dado2=$mensagem where id_problema = $problema");
            $resposta = "São eventos distintos?";
            atualizar_etapa($id, 3.07);
        }
    }*/
    if($etapa == 3.07){
        if($mensagem >0){
            $problema = ultimoproblema($id);
            definirdado($problema,3,$mensagem);
            $resposta = "Deseja obter a probabilidade de quantos resultados aceitáveis nestes eventos?";
            atualizar_etapa($id, 3.1);
        }
    }
    if($etapa == 3.1){
        if($mensagem >0){
            $problema = ultimoproblema($id);
            if(obter_categoria($problema) == 2){
                definirdado($problema,3,$mensagem);
            }
            if(obter_categoria($problema) == 3){
                $faces = obterdado($problema,2);
                $faces = $db ->query("select faces from objeto where id_objeto = $faces") ->fetch()["faces"];
                if($faces < $mensagem){
                    $mensagem = $faces;
                }
                definirdado($problema,4,$mensagem);
            }
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
