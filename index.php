<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HOME</title>
  <link rel="stylesheet" href="estilos/style_index.css" />
 
</head>
<body>

  <!-- slider -->
  <input type="radio" name="radio-btn" id="radio1" checked>
  <input type="radio" name="radio-btn" id="radio2">
  <input type="radio" name="radio-btn" id="radio3">
  <input type="radio" name="radio-btn" id="radio4">

  <div class="slider">
    <div class="slides">
      <div class="slide">
        <img src="imagem_fundo/008.jpg" alt="Imagem 1">
      </div>
      <div class="slide">
        <img src="imagem_fundo/01.png" alt="Imagem 2">
      </div>
      <div class="slide">
        <img src="imagem_fundo/09.jpg" alt="Imagem 3">
      </div>
      <div class="slide">
        <img src="imagem_fundo/03.webp" alt="Imagem 4">
      </div>
    </div>

    <!-- Botões manuais -->
    <div class="manual-navigation">
      <label for="radio1" class="manual-btn"></label>
      <label for="radio2" class="manual-btn"></label>
      <label for="radio3" class="manual-btn"></label>
      <label for="radio4" class="manual-btn"></label>
    </div>
  </div>

  <!-- Menu sobre o slider -->
  <div class="menu">
    <a href="back\registrar.php">Registrar Mangá</a>
    <a href="back\listar_registro.php">Listar Mangás</a>
    <a href="back\modificar_registro.php">Modificar Registros</a>
  </div>

  <!-- Script automático -->
  <script src="JS/script_index.js"></script>

</body>
</html>
