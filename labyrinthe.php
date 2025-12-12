<?php
session_start();


// rénitialise le compteur
if (isset($_GET['reset'])) {
    session_destroy();
    session_start();
    $_SESSION['deplacements'] = 0;
}


// Initialiser le compteur
if (!isset($_SESSION['deplacements'])) {
    $_SESSION['deplacements'] = 0;
}


// Connexion à la base de données 
$db = new SQLite3('labyrinthe.db');

// Initialisation de la session
if (!isset($_SESSION['cles'])) {
    $_SESSION['cles'] = 0; 
    $_SESSION['deplacements']++;

}

if (!isset($_SESSION['position'])) {
    $result = $db->query("SELECT id FROM couloir WHERE type = 'depart'");
    $depart = $result->fetchArray();
    $_SESSION['position'] = $depart[0];
}

if (!isset($_SESSION['deplacements'])) {
    $_SESSION['deplacements'] = 0;
}

// Gestion des mouvements
if (isset($_GET['move_to'])) {
    $nouvellePosition = $_GET['move_to'];

    // Vérifier si la nouvelle position est une clé
    $result = $db->query("SELECT type FROM couloir WHERE id = $nouvellePosition");
    $typeNouvelleCase = $result->fetchArray()[0];

    if ($typeNouvelleCase === 'cle') {
        $_SESSION['cles']++;
    }

    // Vérifier si le passage est une grille
    $result = $db->query("SELECT type FROM passage WHERE (couloir1 = {$_SESSION['position']} AND couloir2 = $nouvellePosition) OR (couloir1 = $nouvellePosition AND couloir2 = {$_SESSION['position']})");
    $typePassage = $result->fetchArray()[0];

    if ($typePassage !== 'grille' || $_SESSION['cles'] > 0) {
        $_SESSION['position'] = $nouvellePosition;
        $_SESSION['deplacements']++;
    }

    if ($nouvellePosition == 23 && $_SESSION['cles'] >= 2) {
        header("Location: fin.php");
        exit;
    }
}

// Récupérer les voisins accessibles
$result = $db->query("SELECT couloir2 FROM passage WHERE couloir1 = {$_SESSION['position']} UNION SELECT couloir1 FROM passage WHERE couloir2 = {$_SESSION['position']}");
$voisins = [];
while ($row = $result->fetchArray()) {
    $voisins[] = $row[0];
}

// Récupérer le type de la case actuelle
$result = $db->query("SELECT type FROM couloir WHERE id = {$_SESSION['position']}");
$typeCouloirActuel = $result->fetchArray()[0];
?>




<!DOCTYPE html>
<html>
<head>
    <title>Labyrinthe - Couloir <?php echo $_SESSION['position']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Couloir <?php echo $_SESSION['position']; ?></h1>
    <p>Type: <?php echo $typeCouloirActuel; ?></p>
    <p>Clés récupérées: <?php echo $_SESSION['cles']; ?>/2</p>
    <p>Déplacements: <?= $_SESSION['deplacements'] ?></p>


    <h2>Mouvements possibles:</h2>
    <div class="mouvements">
        <?php foreach ($voisins as $voisin): ?>
            <a href="?move_to=<?php echo $voisin; ?>" class="button">Aller en <?php echo $voisin; ?></a>
        <?php endforeach; ?>
    </div>
</body>
</html>
