<?php
require "conexaoMysql.php";

// Verifica se o usuário está logado
session_start();
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    exit("Usuário não autenticado.");
}

// Obtém os dados do formulário
$marca = $_POST['veiculoMarca'] ?? '';
$modelo = $_POST['veiculoModelo'] ?? '';
$ano = $_POST['veiculoAno'] ?? '';
$cor = $_POST['veiculoCor'] ?? '';
$km = $_POST['veiculoKm'] ?? '';
$valor = $_POST['veiculoValor'] ?? '';
$estado = $_POST['estado'] ?? '';
$cidade = $_POST['veiculoCidade'] ?? '';
$descricao = $_POST['veiculoDesc'] ?? '';

// Conecta ao banco de dados
$pdo = mysqlConnect();

// Inicia a transação para garantir a integridade dos dados
try {
    $pdo->beginTransaction();

    // Insere o anúncio na tabela 'Anuncio'
    $stmt = $pdo->prepare("INSERT INTO Anuncio (Marca, Modelo, Ano, Cor, Quilometragem, Descricao, Valor, Estado, Cidade, DataHora, IdAnunciante) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), (SELECT Id FROM Anunciante WHERE Email = ?))");
    $stmt->execute([$marca, $modelo, $ano, $cor, $km, $descricao, $valor, $estado, $cidade, $_SESSION['user']]);
    $idAnuncio = $pdo->lastInsertId(); // Obtém o ID do anúncio inserido

    // Define o caminho da pasta 'images' onde as fotos serão salvas
    $pastaDestino = "./images";

    // Verifica se há arquivos para fazer o upload
    if (isset($_FILES['veiculoFoto'])) {
        // Verifica se o campo de arquivos é um único arquivo ou múltiplos arquivos
        $arquivos = is_array($_FILES['veiculoFoto']['name']) ? $_FILES['veiculoFoto']['name'] : [$_FILES['veiculoFoto']['name']]; // Normaliza para sempre ser um array

        foreach ($arquivos as $i => $nomeArquivo) {
            // Gera um nome único para o arquivo
            $nomeNovoArquivo = uniqid() . "_" . $nomeArquivo;
            $caminhoDestino = $pastaDestino . $nomeNovoArquivo;

            // Move o arquivo para a pasta 'images'
            if (move_uploaded_file($_FILES['veiculoFoto']['tmp_name'][$i] ?? $_FILES['veiculoFoto']['tmp_name'], $caminhoDestino)) {
                // Salva o nome do arquivo na tabela 'Foto'
                $stmtFoto = $pdo->prepare("INSERT INTO Foto (IdAnuncio, NomeArqFoto) VALUES (?, ?)");
                $stmtFoto->execute([$idAnuncio, $nomeNovoArquivo]);
            } else {
                // Caso o upload de alguma imagem falhe
                throw new Exception("Erro ao salvar a imagem: $nomeArquivo");
            }
        }
    }

    // Commit da transação (grava as alterações no banco de dados)
    $pdo->commit();

    echo "Anúncio cadastrado com sucesso!";
} catch (Exception $e) {
    // Se ocorrer um erro, faz o rollback e exibe a mensagem de erro
    $pdo->rollBack();
    echo "Erro ao cadastrar o anúncio: " . $e->getMessage();
}
?>
