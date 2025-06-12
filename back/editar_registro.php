<?php
session_start();

// Verifica se o usuário está tentando acessar diretamente sem um ID válido
if (!isset($_POST['id']) && !isset($_GET['id'])) {
    header("Location: modificar_registro.php");
    exit;
}

// Conexão com o banco de dados
include("../conexao/conexao.php");

// Busca os dados atuais do mangá ANTES de processar o POST
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$sql_current = "SELECT imagemManga FROM MANGAS WHERE id = ?";
$stmt_current = $conn->prepare($sql_current);
$stmt_current->bind_param("s", $id);
$stmt_current->execute();
$result_current = $stmt_current->get_result();

if ($result_current->num_rows == 0) {
    header("Location: modificar_registro.php");
    exit;
}

$current_data = $result_current->fetch_assoc();
$current_image = $current_data['imagemManga'];

// Processamento do formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $id = $_POST["id"];
        $nomeManga = $_POST["nomeManga"];
        $nomeAutor = $_POST["nomeAutor"];
        $anoLanc = $_POST["anoLanc"];
        $volumes = $_POST["volumes"];
        $generoManga = $_POST["generoManga"];
        $sinopse = $_POST["sinopse"];

        // Verifica se uma nova imagem foi enviada
        if ($_FILES["imagemManga"]["size"] > 0) {
            $diretorio = "../imagem_capa/";
            $imagemTmp = $_FILES["imagemManga"]["tmp_name"];
            $tamanhoArquivo = $_FILES["imagemManga"]["size"];
            $extensao = strtolower(pathinfo($_FILES["imagemManga"]["name"], PATHINFO_EXTENSION));
            $nomeImagem = uniqid("img_", true) . "." . $extensao;
            $caminhoCompleto = $diretorio . $nomeImagem;

            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            $tamanhoMaximo = 2 * 1024 * 1024; // 2MB

            // Validações da imagem
            if (!getimagesize($imagemTmp)) {
                throw new Exception("O arquivo enviado não é uma imagem válida.");
            }
            if (!in_array($extensao, $extensoesPermitidas)) {
                throw new Exception("Formato inválido. Use JPG, JPEG, PNG ou GIF.");
            }
            if ($tamanhoArquivo > $tamanhoMaximo) {
                throw new Exception("Imagem muito grande. Máximo permitido: 2 MB.");
            }
            if (!move_uploaded_file($imagemTmp, $caminhoCompleto)) {
                throw new Exception("Erro ao mover o arquivo de imagem.");
            }

            // Remove a imagem antiga com segurança
            if (!empty($current_image) && file_exists($diretorio . $current_image)) {
                if (strpos($current_image, '..') === false) { // Prevenção contra path traversal
                    @unlink($diretorio . $current_image);
                }
            }

            // Atualiza com a nova imagem
            $sql = "UPDATE MANGAS SET nomeManga=?, nomeAutor=?, anoLanc=?, volumes=?, generoManga=?, sinopse=?, imagemManga=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $nomeManga, $nomeAutor, $anoLanc, $volumes, $generoManga, $sinopse, $nomeImagem, $id);
        } else {
            // Atualiza sem alterar a imagem
            $sql = "UPDATE MANGAS SET nomeManga=?, nomeAutor=?, anoLanc=?, volumes=?, generoManga=?, sinopse=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $nomeManga, $nomeAutor, $anoLanc, $volumes, $generoManga, $sinopse, $id);
        }

        $stmt->execute();
        $_SESSION["mensagem_sucesso"] = "Mangá " . htmlspecialchars($nomeManga) . " atualizado com sucesso.";
        header("Location: modificar_registro.php");
        exit;

    } catch (Exception $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Busca todos os dados do mangá para exibição no formulário
$sql = "SELECT * FROM MANGAS WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: modificar_registro.php");
    exit;
}

$manga = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mangá</title>
    <link rel="stylesheet" href="../estilos/style_editar.css">
</head>
<body>
    <div class="menu-container">
        <nav class="menu">
            <a href="../index.php">HOME</a>
            <a href="registrar.php">Registrar Mangá</a>
            <a href="listar_registro.php">Listar Mangás</a>
            <a href="modificar_registro.php">Modificar Registros</a>
        </nav>
    </div>

    <div class="container-edicao">
        <div class="card-manga-ampliado">
            <img src="../imagem_capa/<?php echo htmlspecialchars($manga['imagemManga']); ?>" alt="Capa do Mangá">
            <h2><?php echo htmlspecialchars($manga['nomeManga']); ?></h2>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($manga['nomeAutor']); ?></p>
            <p><strong>Ano:</strong> <?php echo htmlspecialchars($manga['anoLanc']); ?></p>
            <p><strong>Volumes:</strong> <?php echo htmlspecialchars($manga['volumes']); ?></p>
            <p><strong>Gênero:</strong> <?php echo htmlspecialchars($manga['generoManga']); ?></p>
        </div>
        
        <div class="formulario-edicao">
            <h2>Editar Informações</h2>
            <form action="editar_registro.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($manga['id']); ?>">
                
                <div class="form-group">
                    <label for="nomeManga">Nome do Mangá:</label>
                    <input type="text" name="nomeManga" id="nomeManga" value="<?php echo htmlspecialchars($manga['nomeManga']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nomeAutor">Nome do Autor:</label>
                    <input type="text" name="nomeAutor" id="nomeAutor" value="<?php echo htmlspecialchars($manga['nomeAutor']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="anoLanc">Ano de Lançamento:</label>
                    <input type="number" name="anoLanc" id="anoLanc" min="1814" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($manga['anoLanc']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="volumes">Volumes:</label>
                    <input type="number" name="volumes" id="volumes" min="0" step="1" value="<?php echo htmlspecialchars($manga['volumes']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="sinopse">Sinopse:</label>
                    <textarea name="sinopse" id="sinopse" rows="4" required><?php echo htmlspecialchars($manga['sinopse']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="generoManga">Gênero:</label>
                    <select name="generoManga" id="generoManga" required>
                        <option value="acao" <?php echo $manga['generoManga'] == 'acao' ? 'selected' : ''; ?>>Ação</option>
                        <option value="aventura" <?php echo $manga['generoManga'] == 'aventura' ? 'selected' : ''; ?>>Aventura</option>
                        <option value="comedia" <?php echo $manga['generoManga'] == 'comedia' ? 'selected' : ''; ?>>Comédia</option>
                        <option value="fantasia" <?php echo $manga['generoManga'] == 'fantasia' ? 'selected' : ''; ?>>Fantasia</option>
                        <option value="horror" <?php echo $manga['generoManga'] == 'horror' ? 'selected' : ''; ?>>Horror</option>
                        <option value="mecha" <?php echo $manga['generoManga'] == 'mecha' ? 'selected' : ''; ?>>Mecha</option>
                        <option value="terror" <?php echo $manga['generoManga'] == 'terror' ? 'selected' : ''; ?>>Terror</option>
                        <option value="esporte" <?php echo $manga['generoManga'] == 'esporte' ? 'selected' : ''; ?>>Esporte</option>
                        <option value="escolar" <?php echo $manga['generoManga'] == 'escolar' ? 'selected' : ''; ?>>Escolar</option>
                        <option value="ficcao" <?php echo $manga['generoManga'] == 'ficcao' ? 'selected' : ''; ?>>Ficção científica</option>
                        <option value="jogo" <?php echo $manga['generoManga'] == 'jogo' ? 'selected' : ''; ?>>Jogo</option>
                        <option value="seinen" <?php echo $manga['generoManga'] == 'seinen' ? 'selected' : ''; ?>>Seinen</option>
                        <option value="shounen" <?php echo $manga['generoManga'] == 'shounen' ? 'selected' : ''; ?>>Shōnen</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="imagemManga">Alterar Imagem da Capa:</label>
                    <input type="file" name="imagemManga" id="imagemManga" accept="image/*">
                    <p class="hint">Deixe em branco para manter a imagem atual</p>
                </div>
                
                <button type="submit" class="btn-atualizar" onclick="return confirm('Tem certeza que deseja atualizar este mangá?')">Atualizar Mangá</button>
                
                <?php if (isset($erro)): ?>
                    <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

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