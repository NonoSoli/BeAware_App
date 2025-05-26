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
    <link rel="stylesheet" href="assets/styles/css/main.css">
</head>
<body>
    <div class="container">
        <h2>Bonjour <?php echo $_SESSION['username']?></h2>
        <h2>vous souhaitez :</h2>
        <div class="button-group">
            <form action="action.php" method="get">
                <button type="submit" name="action" value="create">Créer</button>
                <button type="submit" name="action" value="update">Modifier</button>
                <button type="submit" name="action" value="delete">Supprimer</button>
            </form>
        </div>
    </div>
</body>
</html>
