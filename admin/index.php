<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BE Aware - Admin</title>
    <link rel="stylesheet" href="../assets/styles/css/admin.css">
    <style>
        .message-success {
            color: green;
            font-weight: bold;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php">Se déconnecter</a>
        <a href="../home.php">Vers l'application</a>
        <h2>Bonjour <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <h2>Vous souhaitez :</h2>

        <?php if (isset($_SESSION['export_status'])): ?>
            <p class="message-success"><?= $_SESSION['export_status']; ?></p>
            <?php unset($_SESSION['export_status']); ?>
        <?php endif; ?>

        <div class="button-group">
            <form action="action.php" method="get" class="main-actions">
                <button type="submit" name="action" value="create">➕ Créer</button>
                <button type="submit" name="action" value="update">✏️ Modifier</button>
                <button type="submit" name="action" value="delete">🗑️ Supprimer</button>
            </form>
        </div>

        <div class="export-button">
            <form method="post" action="../assets/json/export_json.php">
                <button type="submit">🛠 Mettre à jour les données</button>
            </form>
        </div>
    </div>
</body>
</html>
