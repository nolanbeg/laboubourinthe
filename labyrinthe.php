<?php
session_start();

// Connexion à la base de données SQLite
$db = new SQLite3('labyrinthe.db');

// Initialisation de la session
if (!isset($_SESSION['cles'])) {
    $_SESSION['cles'] = [];
}

if (!isset($_SESSION['position'])) {
    $result = $db->query("SELECT id FROM couloir WHERE type = 'depart'");
    $depart = $result->fetchArray(SQLITE3_ASSOC);
    $_SESSION['position'] = $depart['id'];
}

if (!isset($_SESSION['deplacements'])) {
    $_SESSION['deplacements'] = 0;
}

// Charger les données du labyrinthe
$couloirs = [];
$result = $db->query("SELECT id, type FROM couloir");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $couloirs[$row['id']] = ['type' => $row['type'], 'voisins' => []];
}

$result = $db->query("SELECT couloir1, couloir2, type FROM passage");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $couloirs[$row['couloir1']]['voisins'][] = ['id' => $row['couloir2'], 'type' => $row['type']];
    $couloirs[$row['couloir2']]['voisins'][] = ['id' => $row['couloir1'], 'type' => $row['type']];
}

// Gestion des mouvements
if (isset($_GET['move_to'])) {
    $nouvellePosition = intval($_GET['move_to']);
    $typeNouvelleCase = $couloirs[$nouvellePosition]['type'];

    if ($typeNouvelleCase === 'cle') {
        if (!in_array($nouvellePosition, $_SESSION['cles'])) {
            array_push($_SESSION['cles'], $nouvellePosition);
        }
    }

    // Vérifier si le passage est une grille et si le joueur a la clé
    foreach ($couloirs[$_SESSION['position']]['voisins'] as $voisin) {
        if ($voisin['id'] === $nouvellePosition) {
            if ($voisin['type'] !== 'grille' || in_array($nouvellePosition, $_SESSION['cles'])) {
                $_SESSION['position'] = $nouvellePosition;
                $_SESSION['deplacements']++;
            }
            break;
        }
    }

    if ($nouvellePosition === 23 && count($_SESSION['cles']) >= 2) {
        header("Location: fin.php");
        exit;
    }
}

// Afficher le couloir actuel
$couloirActuel = $couloirs[$_SESSION['position']];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labyrinthe - Couloir <?php echo $_SESSION['position']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Couloir <?php echo $_SESSION['position']; ?></h1>
    <p>Type: <?php echo $couloirActuel['type']; ?></p>
    <p>Clés récupérées: <?php echo count($_SESSION['cles']); ?>/2</p>
    <p>Déplacements: <?php echo $_SESSION['deplacements']; ?></p>

    <h2>Mouvements possibles:</h2>
    <div class="mouvements">
        <?php foreach ($couloirActuel['voisins'] as $voisin): ?>
            <a href="?move_to=<?php echo $voisin['id']; ?>" class="button">
                Aller en <?php echo $voisin['id']; ?>
            </a>
        <?php endforeach; ?>
    </div>
</body>
</html>
