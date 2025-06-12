<?php
if (isset($_POST['id'])) {
    include("../conexao/conexao.php");
    $id = $_POST['id'];

    //Buscar o nome da imagem
    $sqlSelect = "SELECT imagemManga FROM MANGAS WHERE id = ?";
    $stmtSelect = $conn->prepare($sqlSelect);

    if ($stmtSelect) {
        $stmtSelect->bind_param("s", $id);
        $stmtSelect->execute();
        $resultado = $stmtSelect->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $nomeImagem = $row['imagemManga'];
            $caminhoImagem = "../imagem_capa/" . $nomeImagem;

            //Excluir o arquivo da imagem, se existir
            if (file_exists($caminhoImagem)) {
                unlink($caminhoImagem);
            }
        }

        $stmtSelect->close();
    }

    //Deletar o registro no banco
    $sqlDelete = "DELETE FROM MANGAS WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);

    if ($stmtDelete) {
        $stmtDelete->bind_param("s", $id);
        $stmtDelete->execute();
        $stmtDelete->close();
    }

    $conn->close();

    // Redirecionar após excluir
    header("Location: modificar_registro.php");
    exit;
}
?>