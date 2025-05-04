<?php

class Anunciante
{

  static function Create($pdo, $nome, $cpf, $email, $senhaHash, $telefone)
  {
    $stmt = $pdo->prepare(
      <<<SQL
      INSERT INTO Anunciante (Nome, CPF, Email, SenhaHash, Telefone)
      VALUES (?, ?, ?, ?, ?)
      SQL
    );
    
    $stmt->execute([$nome, $cpf, $email, $senhaHash, $telefone]);
    
    return $pdo->lastInsertId();
  }
}

 
