<!DOCTYPE html>
<html>
<head>
    <title>Fin de la partie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Félicitations !</h1>
    <p>Vous avez trouvé la sortie en <?php echo $_SESSION['deplacements']; ?> déplacements.</p>
    <a href="index.php" class="button">Recommencer</a>
</body>
</html>
