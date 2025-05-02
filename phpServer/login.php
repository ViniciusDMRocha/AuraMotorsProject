<?php

require "conexaoMysql.php";

class LoginResult
{
  public $success;
  public $newLocation;

  function __construct($success, $newLocation)
  {
    $this->success = $success;
    $this->newLocation = $newLocation;
  }
}

function checkUserCredentials($pdo, $email, $senha)
{
  $sql = <<<SQL
    SELECT Id, SenhaHash
    FROM Anunciante
    WHERE Email = ? 
  SQL;

  try {
    // Preparando a consulta para obter o Id e SenhaHash do Anunciante
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) 
      return false; // email não encontrado

    // Verifica se a senha informada é válida
    if (!password_verify($senha, $user['SenhaHash']))
      return false; // senha incorreta
    
    // Se as credenciais forem corretas, retorna o id do usuário
    return $user['Id']; // Retorna o ID do usuário
  } 
  catch (Exception $e) {
    exit('Falha inesperada: ' . $e->getMessage());
  }
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$pdo = mysqlConnect();
$userId = checkUserCredentials($pdo, $email, $senha);

if ($userId) {
  session_start();
  $_SESSION['loggedIn'] = true;
  $_SESSION['user'] = $email; // Guarda o e-mail
  $_SESSION['userId'] = $userId; // Guarda o ID do usuário
  $response = new LoginResult(true, './menuRestrito.html');
} else {
  $response = new LoginResult(false, '');
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>
