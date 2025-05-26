<?php
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 3 || strlen($password) < 4) {
        $message = "Nom ou mot de passe trop court.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Utilisateur créé avec succès.";
        } else {
            $message = "Erreur : nom déjà utilisé ou problème de BDD.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un utilisateur</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
</head>
<body>
    <div class="register-container">
        <h2>Nouvel utilisateur</h2>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Créer</button>
        </form>
    </div>
</body>
</html>
