<?php

class Anuncio
{

    static function Create($pdo, $marca, $modelo, $ano, $cor, $km, $valor, $estado, $cidade, $descricao, $foto)
    {
        
        try {
            session_start();
            $pdo->beginTransaction();
        
            $stmt1 = $pdo->prepare(
                <<<SQL
                INSERT INTO Anuncio (Marca, Modelo, Ano, Cor, Quilometragem, Descricao, Valor, Estado, Cidade, DataHora, IdAnunciante)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                SQL
            );
            $stmt1->execute([$marca, $modelo, $ano, $cor, $km, $valor, $estado, $cidade, $descricao, $_SESSION['userId']]);

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
}