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

  static function ListarMeusInteresses($pdo, $id) {
    $sql = <<<SQL
        SELECT 
            Nome AS nome,
            Telefone AS telefone,
            Mensagem AS mensagem
        FROM Interesse
        WHERE IdAnuncio = ?
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }
}