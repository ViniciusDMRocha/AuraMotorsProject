<?php

class Interesse
{

  static function Create($pdo, $nome, $telefone, $mensagem, $idAnuncio)
  {
    $stmt = $pdo->prepare(
      <<<SQL
      INSERT INTO Interesse (Nome, Telefone, Mensagem, DataHora, IdAnuncio)
      VALUES (?, ?, ?, NOW(), ?)
      SQL
    );

    $stmt->execute([$nome, $telefone, $mensagem, $idAnuncio]);

    return $pdo->lastInsertId();
  }
}