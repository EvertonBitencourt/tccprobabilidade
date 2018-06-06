<?php
/**
 * Created by PhpStorm.
 * User: Everton
 * Date: 26/05/2018
 * Time: 11:31
 */

function dialogo($id, $mensagem){



    $resposta = "Não entendi sua mensagem. Por favor, digite novamente, verificando sua ortografia.";
    $db = abrir_banco();

    $consulta_etapa = $db ->query("SELECT etapa from usuario where id_usuario = $id")->fetch();
    $etapa = $consulta_etapa[0];
    if(verificar_palavra($mensagem,"cancelar")){
        $resposta = "Você resolveu cancelar a atividade, O que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.";
        atualizar_etapa($id, 1);
    }
    if($etapa == 0.5){
        if(verificar_palavra($mensagem,"nao")){
            $resposta = "O que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.";
            atualizar_etapa($id, 1);
        }
        if(verificar_palavra($mensagem,"sim")){
            $resposta = "Qual a sua turma?";
            atualizar_etapa($id, 0.6);
        }
    }
    if($etapa == 0.6){
        $turma = verificar_turma($mensagem,$id);
        if($turma = 0){
            $resposta = "Digite uma turma válida.";
        }else{
            $resposta = "O que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.";
            atualizar_etapa($id,1);
        }
    }
    if($etapa == 1){
        if($mensagem == 1 || ctexto($mensagem, "Ver material de apoio",5)){
            $resposta = "Consulte um dos sites da lista abaixo: \nhttps://brasilescola.uol.com.br/matematica/probabilidade.htm";
            $etapa = "a1";
        }
        if($mensagem == 2 || ctexto($mensagem, "Resolver Problema",5)){
            $resposta = "Qual a categoria do seu problema? \n Probabilidade \n Espaço Amostral";
            atualizar_etapa($id,2);
        }
        if($mensagem == 3 || ctexto($mensagem, "Ver exercícios resolvidos",5)){
            $resposta = "Qual a categoria do seu problema? \n Probabilidade \n Espaço Amostral";
            atualizar_etapa($id,1.2);
        }
    }
    if($etapa == 1.2){
        $categoria = verificarCategoria($mensagem,$id);
        if($categoria == 2){
            $resposta = "Acesse http://approbabilidade.herokuapp.com/exerciciosprontos.php?texto=e";
            $etapa = "a1";
        }
        if($categoria == 3){
            $resposta = "Acesse http://approbabilidade.herokuapp.com/exerciciosprontos.php";
            $etapa = "a1";
        }
    }
    if($etapa == 2){
        $categoria = verificarCategoria($mensagem,$id);
        if($categoria == 2 || $categoria == 3){
            $resposta = "Para lhe ajudar melhor preciso saber algumas informações de seu problema. Por favor, responda claramente os próximos questionamentos.\nVocê irá retirar um objeto de um conjunto ou lançar algo (moeda, dado)?";
            atualizar_etapa($id, 2.5);
        }
        if($categoria == 1){

        }
    }
    if($etapa == 2.5){
        if(ctexto($mensagem, "lançar",3)){
            $problema = ultimoproblema($id);
            definirdado($problema,1,$mensagem);
            $resposta = "Qual objeto será lançado?";
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
            $resposta = "Atenção! Você precisa ter pelo menos dois objetos distintos!\nInforme uma nova quantidade de objetos:";
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
                        $resposta = "Qual nome do próximo objeto a ser retirado?";
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
                if(identificarobjeto($problema,$mensagem) === false){
                    $resposta = "Esse objeto não existe no conjunto que estamos retirando. Diga o nome correto.";
                    break;
                }
                definirdado($problema, $idado + 1, identificarobjeto($problema,$mensagem)); //passando o nome do objeto a ser retirado
                if ($qretiradas == $idado - $consulta) {
                    $resposta = "Você irá repor o objeto a cada retirada ou não?";
                    atualizar_etapa($id, 2.96);
                } else{
                    $resposta = "Qual próximo objeto que deseja retirar?";
                }
                $flag = false;
            }else $idado++;
        }
    }
    if($etapa == 2.96){
        if(verificar_palavra($mensagem,"nao")){
            $problema = ultimoproblema($id);
            $consulta = obterdado($problema,2)*2+3;
            $qretiradas =obterdado($problema,$consulta);
            definirdado($problema,$qretiradas+$consulta+1,"false");
            $resposta = "Deseja uma resposta detalhada?";
            atualizar_etapa($id,3.2);
        }
        if(verificar_palavra($mensagem,"sim")){
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
    if($etapa == 3.07){
        if($mensagem >0){
            $problema = ultimoproblema($id);
            definirdado($problema,3,$mensagem);
            $resposta = "Qual a quantidade de eventos favoráveis por lançamento?";
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
            $resposta = "Você deseja obter uma resposta detalhada?";
            atualizar_etapa($id, 3.2);
        }
    }
    if($etapa == 3.2){
        if(verificar_palavra($mensagem,"nao")){
            $resposta = resolver($id, false);
            $etapa = "a1";
        }
        if(verificar_palavra($mensagem,"sim")){
            $resposta = resolver($id, true);
            $etapa = "a1";
        }
    }
    if($etapa == "a1"){
        $resposta = $resposta."\nDeseja fazer outra coisa?";
        atualizar_etapa($id, 4);
    }
    if($etapa == 4){
        if(verificar_palavra($mensagem,"sim")){
            atualizar_etapa($id, 1);
            $resposta ="O que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.\nDigite o número da opção.";
        }
        if(verificar_palavra($mensagem,"nao")){
            atualizar_etapa($id, 5);
            $resposta = "Ok, até logo!";
        }
    }
    if($etapa == 5){
        atualizar_etapa($id,1);
        $resposta = "Veja só quem voltou!\nO que você deseja fazer?\n1 - Ver material de apoio.\n2 - Resolver problema\n3 - Ver Exercicio Resolvido.\nDigite o número da opção.";
    }

    return $resposta;
}
