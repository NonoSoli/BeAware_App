<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? 'create';

$actionText = [
    'create' => 'Créer',
    'update' => 'Modifier',
    'delete' => 'Supprimer'
][$action] ?? 'Créer';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($actionText) ?> un élément</title>
    <link rel="stylesheet" href="../assets/styles/css/admin.css">
</head>
<body>
    <div class="container">
        <a href="index.php">Vers l'accueil</a>
        <div class="container">
            <h2><?= htmlspecialchars($actionText) ?> quoi ?</h2>
            <div class="button-group">
                <form action="redirect.php" method="get">
                    <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
                    <button type="submit" name="target" value="domain">Domaine</button>
                    <button type="submit" name="target" value="level">Niveau</button>
                    <button type="submit" name="target" value="exercise">Exercice</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
