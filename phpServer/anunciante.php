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

  // // Busca um cliente na tabela a partir do Id e retorna
  // // os dados na forma de um objeto PHP.
  // static function Get($pdo, $id)
  // {
  //   $stmt = $pdo->prepare(
  //     <<<SQL
  //     SELECT id, nome, cpf, email, senhaHash, DATE_FORMAT(dataNascimento, "%d-%m-%Y") as dataNascimento, estadoCivil, altura
  //     FROM cliente
  //     WHERE id = ?
  //     SQL
  //   );

  //   $stmt->execute([$id]);
  //   if ($stmt->rowCount() == 0)
  //     throw new Exception("Cliente não localizado");

  //   $cliente = $stmt->fetch(PDO::FETCH_OBJ);
  //   return $cliente;
  // }

  // // Retorna os 30 clientes iniciais da tabela na forma de um array de objetos.
  // static function GetFirst30($pdo)
  // {
  //   // Neste exemplo não é necessário utilizar prepared statements
  //   // porque não há a possibilidade de inj. de S Q L, 
  //   // pois nenhum parâmetro do usuário é utilizado na query SQL. 
  //   $stmt = $pdo->query(
  //     <<<SQL
  //     SELECT id, nome, cpf, email, senhaHash, DATE_FORMAT(dataNascimento, "%d-%m-%Y") as dataNascimento, estadoCivil, altura
  //     FROM cliente
  //     LIMIT 30
  //     SQL
  //   );

  //   // Resgata os dados dos clientes como um array de objetos
  //   $arrayClientes = $stmt->fetchAll(PDO::FETCH_OBJ);
  //   return $arrayClientes;
  // }

  // // Método estático para excluir um cliente dado o seu Id
  // public static function Remove($pdo, $id)
  // {
  //   $sql = <<<SQL
  //   DELETE 
  //   FROM cliente
  //   WHERE id = ?
  //   LIMIT 1
  //   SQL;

  //   // Necessário utilizar prepared statements devido ao 
  //   // parâmetro informado pelo usuário
  //   $stmt = $pdo->prepare($sql);
  //   $stmt->execute([$id]);
  // }
}
