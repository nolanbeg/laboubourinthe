<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fin de la partie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Félicitations !</h1>
    <p>Tu as trouvé la sortie du labyrinthe en <?= $_SESSION['deplacements'] ?> coups.</p>
    <a href="labyrinthe.php?reset=1" class="button">Rejouer</a>
</body>
</html>
