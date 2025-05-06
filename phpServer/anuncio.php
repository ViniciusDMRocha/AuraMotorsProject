<?php

class Anuncio
{

    static function Create($pdo, $marca, $modelo, $ano, $cor, $km, $descricao, $valor, $estado, $cidade, $foto)
    {
        
        try {
            session_start();
            $pdo->beginTransaction();
        
            $stmt1 = $pdo->prepare(
                <<<SQL
                INSERT INTO Anuncio (Marca, Modelo, Ano, Cor, Quilometragem, Descricao, Valor, DataHora, Estado, Cidade, IdAnunciante)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)
                SQL
            );
            $stmt1->execute([$marca, $modelo, $ano, $cor, $km, $descricao, $valor, $estado, $cidade, $_SESSION['userId']]);

            $idAnuncio = $pdo->lastInsertId();
            $stmt2 = $pdo->prepare(
                <<<SQL
                INSERT INTO Foto (IdAnuncio, NomeArqFoto)
                VALUES (?, ?)
                SQL
            );
            print_r($foto);
            foreach ($foto['tmp_name'] as $i => $tmpName) {
                if ($foto['error'][$i] === UPLOAD_ERR_OK) {
                    $nomeOriginal = basename($foto['name'][$i]);
                    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                    $modeloSlug = preg_replace('/\s+/', '-', trim($modelo));
            
                    $nome = uniqid() . $marca . "-" . $modeloSlug . "." . $extensao;
                    $destino = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $nome;
            
                    if (move_uploaded_file($tmpName, $destino)) {
                    $stmt2->execute([$idAnuncio, $nome]);
                    } else {
                    throw new Exception("Erro ao mover o arquivo para o diretório de destino.");
                    }
                } else {
                    throw new Exception("Erro no upload do arquivo: " . $foto['error'][$i]);
                }
            }
    
            $pdo->commit();
        
            echo "Anúncio cadastrado com sucesso!";

        } catch (Exception $e) {
            // Se ocorrer um erro, faz o rollback e exibe a mensagem de erro
            $pdo->rollBack();
            echo "Erro ao cadastrar o anúncio: " . $e->getMessage();
        }
    }

    static function ListarAnuncios($pdo, $marca, $modelo, $local) {
        $sql = "
            SELECT 
                a.Id AS id,
                a.Marca AS marca,
                a.Modelo AS modelo,
                a.Ano AS ano,
                a.Valor AS preco,
                a.Cidade AS localizacao,
                (SELECT f.NomeArqFoto 
                 FROM Foto f 
                 WHERE f.IdAnuncio = a.Id 
                 LIMIT 1) AS imagem
            FROM Anuncio a WHERE 1=1
        ";
    
        $parametros = [];

        if($marca){
            $sql .= " AND a.marca = ?";
            $parametros[]= $marca;
        };

        if($modelo){
            $sql .= " AND a.modelo = ?";
            $parametros[]= $modelo;
        };
        if($local){
            $sql .= " AND a.Cidade = ?";
            $parametros[]= $local;
        };

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarMarcas($pdo) {
        $sql = <<<SQL
            SELECT DISTINCT Marca FROM Anuncio;
        SQL;
    
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarModelos($pdo, $marca) {
        $sql = <<<SQL
            SELECT DISTINCT Modelo FROM Anuncio WHERE marca=?;
        SQL;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marca]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarLocal($pdo, $marca, $modelo) {
        $sql = <<<SQL
            SELECT DISTINCT Cidade FROM Anuncio WHERE marca=? AND modelo=?;
        SQL;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marca, $modelo]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarMeusMarcas($pdo, $userId) {
        $sql = <<<SQL
            SELECT DISTINCT Marca FROM Anuncio WHERE IdAnunciante = ?;
        SQL;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarMeusModelos($pdo, $marca, $userId) {
        $sql = <<<SQL
            SELECT DISTINCT Modelo FROM Anuncio WHERE marca=? AND IdAnunciante = ?;
        SQL;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marca, $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    static function ListarMeusLocal($pdo, $marca, $modelo, $userId) {
        $sql = <<<SQL
            SELECT DISTINCT Cidade FROM Anuncio WHERE marca=? AND modelo=? AND IdAnunciante = ?;
        SQL;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marca, $modelo, $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    
    static function BuscarPorId($pdo, $id) {
        $sqlAnuncio = <<<SQL
            SELECT 
                a.Id AS id,
                a.Marca AS marca,
                a.Modelo AS modelo,
                a.Ano AS ano,
                a.Cor AS cor,
                a.Quilometragem AS km,
                a.Descricao AS descricao,
                a.Valor AS preco,
                a.Cidade AS cidade,
                a.Estado AS estado
            FROM Anuncio a
            WHERE a.Id = ?
        SQL;
    
        $stmt = $pdo->prepare($sqlAnuncio);
        $stmt->execute([$id]);
        $anuncio = $stmt->fetch(PDO::FETCH_OBJ);
    
        $sqlImagens = "SELECT NomeArqFoto AS imagem FROM Foto WHERE IdAnuncio = ?";
        $stmt = $pdo->prepare($sqlImagens);
        $stmt->execute([$id]);
        $imagens = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        $anuncio->imagens = $imagens;
    
        return $anuncio;
    }
    
    static function ListarMeusAnuncios($pdo, $userId, $marca, $modelo, $local) {
        $sql = <<<SQL
            SELECT 
                a.Id AS id,
                a.Marca AS marca,
                a.Modelo AS modelo,
                a.Ano AS ano,
                a.Cidade AS localizacao,
                (SELECT f.NomeArqFoto 
                 FROM Foto f 
                 WHERE f.IdAnuncio = a.Id 
                 LIMIT 1) AS imagem
            FROM Anuncio a
            WHERE a.IdAnunciante = ?
        SQL;
    
        $parametros = [];
        $parametros[]= $userId;

        if($marca){
            $sql .= " AND a.marca = ?";
            $parametros[]= $marca;
        };

        if($modelo){
            $sql .= " AND a.modelo = ?";
            $parametros[]= $modelo;
        };
        if($local){
            $sql .= " AND a.Cidade = ?";
            $parametros[]= $local;
        };

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_OBJ);

    }

    static function excluirAnuncio($pdo, $id){
        try {
            $pdo->beginTransaction();

            $sql = "DELETE 
            FROM Anuncio
            WHERE Id = ?
            LIMIT 1";
            
            $stmt1 = $pdo->prepare($sql);
            $stmt1->execute([$id]);
    
            $sql = "DELETE 
            FROM Foto
            WHERE IdAnuncio = ?";

            $stmt2 = $pdo->prepare($sql);
            $stmt2->execute([$id]);
    
            $sql = "DELETE 
            FROM Interesse
            WHERE IdAnuncio = ?";
            
            $stmt3 = $pdo->prepare($sql);
            $stmt3->execute([$id]);

            $pdo->commit();

        } catch (Exception $e) {
            // Se ocorrer um erro, faz o rollback e exibe a mensagem de erro
            $pdo->rollBack();
            echo "Erro ao excluir o anúncio: " . $e->getMessage();
        }
    }
}