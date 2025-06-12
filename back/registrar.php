<?php
// registrar_manga.php
session_start();

if (isset($_SESSION["mensagem_sucesso"])) {
    echo "<script>alert('" . strip_tags($_SESSION["mensagem_sucesso"]) . "');</script>";
    unset($_SESSION["mensagem_sucesso"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        include("../conexao/conexao.php");

        $nomeManga = $_POST["nomeManga"];
        $nomeAutor = $_POST["nomeAutor"];
        $anoLanc = $_POST["anoLanc"];
        $volumes = $_POST["volumes"];
        $generoManga = $_POST["generoManga"];
        $sinopse = $_POST["sinopse"]; 
        $id = uniqid("1124");

        $diretorio = "../imagem_capa/";
        $imagemTmp = $_FILES["imagemManga"]["tmp_name"];
        $tamanhoArquivo = $_FILES["imagemManga"]["size"];
        $extensao = strtolower(pathinfo($_FILES["imagemManga"]["name"], PATHINFO_EXTENSION));
        $nomeImagem = uniqid("img_", true) . "." . $extensao;
        $caminhoCompleto = $diretorio . $nomeImagem;

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $tamanhoMaximo = 2 * 1024 * 1024;

        if (!getimagesize($imagemTmp)) {
            echo "<script>alert('O arquivo enviado não é uma imagem válida.');</script>";
        } elseif (!in_array($extensao, $extensoesPermitidas)) {
            echo "<script>alert('Formato inválido. Use JPG, JPEG, PNG ou GIF.');</script>";
        } elseif ($tamanhoArquivo > $tamanhoMaximo) {
            echo "<script>alert('Imagem muito grande. Máximo permitido: 2 MB.');</script>";
        } elseif (!move_uploaded_file($imagemTmp, $caminhoCompleto)) {
            echo "<script>alert('Erro ao mover o arquivo de imagem.');</script>";
        } else {
            $sql = "INSERT INTO MANGAS (id, nomeManga, nomeAutor, anoLanc, volumes, generoManga, imagemManga, sinopse)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; // Alterado de descricao para sinopse
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $id, $nomeManga, $nomeAutor, $anoLanc, $volumes, $generoManga, $nomeImagem, $sinopse); // Alterado de descricao para sinopse
            $stmt->execute();

            $_SESSION["mensagem_sucesso"] = "Mangá " . htmlspecialchars($nomeManga) . " registrado com sucesso.";
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        }

    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Erro ao registrar: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Mangá</title>
    <link rel="stylesheet" href="../estilos/style_registrar.css">
</head>
<body>
    <div class="menu-container">
        <nav class="menu">
            <a href="../index.php">HOME</a>
            <a href="listar_registro.php">Listar Mangás</a>
            <a href="modificar_registro.php">Modificar Registros</a>
        </nav>
    </div>

    <main>
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Registro de Mangás</h2>

            <label for="nomeManga">Nome do Mangá:</label>
            <input type="text" name="nomeManga" id="nomeManga" required>

            <label for="nomeAutor">Nome do Autor:</label>
            <input type="text" name="nomeAutor" id="nomeAutor" required>

            <label for="anoLanc">Ano de Lançamento:</label>
            <input type="number" name="anoLanc" id="anoLanc" min="1814" max="2025" required>

            <label for="volumes">Volumes:</label>
            <input type="number" name="volumes" id="volumes" min="0" step="1" required>

            <label for="sinopse">Sinopse:</label> <!-- Alterado de descricao para sinopse -->
            <textarea name="sinopse" id="sinopse" rows="4" required></textarea>

            <label for="generoManga">Gênero:</label>
            <select name="generoManga" id="generoManga" required>
                <option value="acao">Ação</option>
                <option value="aventura">Aventura</option>
                <option value="comedia">Comédia</option>
                <option value="fantasia">Fantasia</option>
                <option value="horror">Horror</option>
                <option value="mecha">Mecha</option>
                <option value="terror">Terror</option>
                <option value="esporte">Esporte</option>
                <option value="escolar">Escolar</option>
                <option value="ficcao">Ficção científica</option>
                <option value="jogo">Jogo</option>
                <option value="seinen">Seinen</option>
                <option value="shounen">Shōnen</option>
            </select>

            <label for="imagemManga">Imagem da Capa:</label>
            <input type="file" name="imagemManga" id="imagemManga" accept="image/*" required>

            <input type="submit" value="Registrar">
        </form>
    </main>

    <!-- Script automático -->
    <div class="slider">
        <div class="slides">
            <div class="slide">
                <img src="../imagem_fundo/008.jpg" alt="Imagem 1">
            </div>
            <div class="slide">
                <img src="../imagem_fundo/01.png" alt="Imagem 2">
            </div>
            <div class="slide">
                <img src="../imagem_fundo/09.jpg" alt="Imagem 3">
            </div>
            <div class="slide">
                <img src="../imagem_fundo/03.webp" alt="Imagem 4">
            </div>
        </div>
    </div>

    <script src="../JS/script_slider.js"></script>
</body>
</html>