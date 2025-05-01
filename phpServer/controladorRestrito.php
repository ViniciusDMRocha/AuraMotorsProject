<?php

require "conexaoMysql.php";
require "anunciante.php";

// resgata a ação a ser executada
$acao = $_GET['acao'];

// conecta ao servidor do MySQL
$pdo = mysqlConnect();

switch ($acao) {
    
  case "exitWhenNotLoggedIn":
    //--------------------------------------------------------------------------------------  
    if (!isset($_SESSION['loggedIn'])) {    // verifica se o loggedIn é true, caso não seja executa o if
      echo json_encode(['sucesso' => false]);
    }
    else{
      echo json_encode(['sucesso' => true]);
    }
    exit();
    break;

    
  case "logout":
    //--------------------------------------------------------------------------------------
    try{
      session_start();
      // apaga as variáveis de sessão de $_SESSION
      session_unset();
      // destrói a sessão e as variáveis fisicamente (em arquivo)
      session_destroy();
      // exclui o cookie da sessão no computador do usuário
      setcookie(session_name(), "", 1, "/");
      // redireciona o usuário para a página de login
      exit();
    }
    catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "listarClientes":
    //--------------------------------------------------------------------------------------
    try {
      $arrayClientes = Cliente::GetFirst30($pdo);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($arrayClientes);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

    //-----------------------------------------------------------------
  default:
    exit("Ação não disponível");
}


?> 