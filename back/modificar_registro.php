<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Registros</title>
    <link rel="stylesheet" href="../estilos/style_modificar.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">HOME</a></li>
                <li><a href="registrar.php">registrar Mangá</a></li>
                <li><a href="listar_registro.php">Listar Mangás</a></li>
            </ul>
        </nav>
    </header>  

    <main>
        <section id="containerSection">
            <!-- Formulário de busca por nome -->
            <form action="modificar_registro.php" method="post">
                <h1>Buscar por Nome do manga</h1>
                <input type="text" name="nomeManga" placeholder="Digite o nome do mangá" required>
                <input type="submit" value="Buscar">
            </form>

            <!-- Formulário de filtro por gênero -->
            <form action="modificar_registro.php" method="post">
                <h1>Filtrar por Gênero</h1>
                <select name="generoManga" required>
                    <option value="todos">Todos os Gêneros</option>
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
                <input type="submit" value="Filtrar">
            </form>
        </section>
        
        <section>
            <?php
            include("../conexao/conexao.php");

            // Verifica se foi enviado o nome
            if (isset($_POST["nomeManga"])) {
                $nomeManga = trim($_POST["nomeManga"]);
                $sql = "SELECT * FROM MANGAS WHERE nomeManga LIKE ? ORDER BY nomeManga ASC";
                $stmt = $conn->prepare($sql);
                $like = "%" . $nomeManga . "%";
                $stmt->bind_param("s", $like);
            }
            // Verifica se foi enviado o gênero
            elseif (isset($_POST["generoManga"])) {
                $generoManga = $_POST["generoManga"];
                if ($generoManga === "todos") {
                    $sql = "SELECT * FROM MANGAS ORDER BY nomeManga ASC";
                    $stmt = $conn->prepare($sql);
                } else {
                    $sql = "SELECT * FROM MANGAS WHERE generoManga = ? ORDER BY nomeManga ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $generoManga);
                }
            }
            
            if (isset($stmt)) {
                $stmt->execute();
                $resultado = $stmt->get_result();
                
                // Exibe os resultados
                if ($resultado->num_rows > 0) {
                    echo "<div class='cards-container'>";
                    while ($row = $resultado->fetch_assoc()) {
                        echo "
                        <div class='card-manga'>
                            <div class='card-img'>
                                <img src='../imagem_capa/" . htmlspecialchars($row['imagemManga']) . "' alt='Capa do Mangá'>
                            </div>
                            <div class='card-info'>
                                <h2>" . htmlspecialchars($row['nomeManga']) . "</h2>
                                <p><strong>Autor:</strong> " . htmlspecialchars($row['nomeAutor']) . "</p>
                                <p><strong>Ano de Lançamento:</strong> " . htmlspecialchars($row['anoLanc']) . "</p>
                                <p><strong>Volumes:</strong> " . htmlspecialchars($row['volumes']) . "</p>
                                <p><strong>Gênero:</strong> " . htmlspecialchars($row['generoManga']) . "</p>
                                <p><strong>Sinopse:</strong></p>
                                <div class='sinopse'>
                                    " . nl2br(htmlspecialchars($row['sinopse'])) . "
                                </div>
                                    <div class='botoes-acoes'>
                                        <a href='editar_registro.php?id=" . htmlspecialchars($row['id']) . "' class='edit-link' onclick='return confirm(\"Deseja editar o mangá: " . addslashes(htmlspecialchars($row['nomeManga'])) . "?\")'>Editar</a>
                                        
                                        <form action='excluir_registro.php' method='post' class='modifica-form'>
                                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                            <button type='submit' class='delete-btn' onclick='return confirm(\"Tem certeza que deseja excluir o mangá: " . addslashes(htmlspecialchars($row['nomeManga'])) . "?\")'>Excluir</button>
                                        </form>
                                    </div>
                            </div>
                        </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='mensagem-aviso'>Nenhum mangá encontrado.</div>";
                }

                $stmt->close();
            }
            // Fecha a conexão com o banco de dados
            $conn->close();
            ?>
        </section>
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