<?php

require "conexaoMysql.php";
require "anunciante.php";
require "anuncio.php";
require "interesse.php";

// resgata a ação a ser executada
$acao = $_GET['acao'];

// conecta ao servidor do MySQL
$pdo = mysqlConnect();

switch ($acao) {
    
  case "exitWhenNotLoggedIn":
   
    session_start();
    if (!isset($_SESSION['loggedIn'])) {
      echo json_encode(['sucesso' => false]);
    }
    else{
      echo json_encode(['sucesso' => true]);
    }
    exit();
    break;

    
  case "logout":
    
    try{
      session_start();
      session_unset();
      session_destroy();
      setcookie(session_name(), "", 1, "/");
      exit();
    }
    catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "adicionarAnuncio":
    //--------------------------------------------------------------------------------------    
    $marca = $_POST['veiculoMarca'] ?? '';
    $modelo = $_POST['veiculoModelo'] ?? '';
    $ano = $_POST['veiculoAno'] ?? '';
    $cor = $_POST['veiculoCor'] ?? '';
    $km = $_POST['veiculoKm'] ?? '';
    $valor = $_POST['veiculoValor'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $cidade = $_POST['veiculoCidade'] ?? '';
    $descricao = $_POST['veiculoDesc'] ?? '';
    $foto = $_FILES['veiculoFoto'] ?? '';

    try {
      Anuncio::Create($pdo, $marca, $modelo, $ano, $cor, $km, $descricao, $valor, $estado, $cidade, $foto);
      header("location: ../listagemAnuncio.html");
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "adicionarInteresse":

    $nome = $_POST["nome"] ?? "";
    $telefone = $_POST["telefone"] ?? "";
    $mensagem = $_POST["mensagem"] ?? "";
    $idAnuncio = $_POST["idAnuncio"] ?? "";

    try {
      Interesse::Create($pdo, $nome, $telefone, $mensagem, $idAnuncio);
      header("Location: ../detalheInteresseRestrito.html?id=" .  $idAnuncio);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "ListarMeusAnuncios":
    try {
      session_start();
      $arrayAnuncios = Anuncio::ListarMeusAnuncios($pdo, $_SESSION['userId']);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($arrayAnuncios);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "ListarMeusInteresses":
    $id = $_GET["id"] ?? "";

    try {
      $arrayInteresses = Interesse::ListarMeusInteresses($pdo, $id);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($arrayInteresses);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "excluirAnuncio":

    $idAnuncio = $_GET["id"] ?? "";
    try {
      Anuncio::excluirAnuncio($pdo, $idAnuncio);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "excluirInteresse":

    $idInteresse = $_GET["id"] ?? "";
    try {
      Interesse::excluirInteresse($pdo, $idInteresse);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;
  
    
  default:
    exit("Ação não disponível");
}


?> 