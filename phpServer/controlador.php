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
    
  case "adicionarAnunciante":

    $nome = $_POST["nome"] ?? "";
    $cpf = $_POST["cpf"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $telefone = $_POST["telefone"] ?? "";

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
      Anunciante::Create($pdo, $nome, $cpf, $email, $senhaHash, $telefone);
      header("location: ../login.html");
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
      header("Location: ../detalheInteresse.html?id=" .  $idAnuncio);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "listarAnuncios":

    $marca = $_POST["marca"] ?? "";
    $modelo = $_POST["modelo"] ?? "";
    $local = $_POST["localizacao"] ?? "";
   
    try {
      $arrayAnuncios = Anuncio::ListarAnuncios($pdo, $marca, $modelo, $local);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($arrayAnuncios);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

  case "buscaAnuncioId":

    $id = $_GET['id'] ?? "";

    try {
      $anuncio = Anuncio::BuscarPorId($pdo, $id);
      header('Content-Type: application/json');
      echo json_encode($anuncio);
      break;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    break;

    case "listarMarcas":
   
      try {
        $arrayMarcas = Anuncio::ListarMarcas($pdo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($arrayMarcas);
      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
      break;

      case "listarModelos":
        $marca = $_GET["marca"];

        try {
          $arrayModelos = Anuncio::ListarModelos($pdo, $marca);
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($arrayModelos);
        } catch (Exception $e) {
          throw new Exception($e->getMessage());
        }
        break;

      case "listarLocal":
        $marca = $_GET["marca"];
        $modelo = $_GET["modelo"];

        try {
          $arrayLocal = Anuncio::ListarLocal($pdo, $marca, $modelo);
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode($arrayLocal);
        } catch (Exception $e) {
          throw new Exception($e->getMessage());
        }
        break;

  default:
    exit("Ação não disponível");
}
