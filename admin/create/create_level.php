<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

$successMessage = '';
$errorMessage = '';

// Récupérer les domaines pour le menu déroulant
$domains = [];
$result = $conn->query("SELECT id, title FROM domains WHERE is_active = 1");
while ($row = $result->fetch_assoc()) {
    $domains[] = $row;
}

// Création de niveau
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title = $_POST['title'] ?? '';
    $fk_domain_id = intval($_POST['fk_domain_id'] ?? 0);
    $time = intval($_POST['time'] ?? 0);
    $difficulty = intval($_POST['difficulty'] ?? 0);

    if ($title && $fk_domain_id && $time && $difficulty) {
        $stmt = $conn->prepare("CALL CreateLevel(?, ?, ?, ?)");
        $stmt->bind_param("siii", $title, $fk_domain_id, $time, $difficulty);
        if ($stmt->execute()) {
            $successMessage = "Niveau créé avec succès.";
        } else {
            $errorMessage = "Erreur lors de la création du niveau.";
        }
    } else {
        $errorMessage = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Niveau</title>
    <link rel="stylesheet" href="../../assets/styles/css/admin.css">
</head>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Créer un Niveau</h2>

        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" required><br><br>

            <label for="fk_domain_id">Domaine :</label>
            <select name="fk_domain_id" id="fk_domain_id" required>
                <option value="">-- Sélectionnez un domaine --</option>
                <?php foreach ($domains as $domain): ?>
                    <option value="<?= $domain['id'] ?>"><?= htmlspecialchars($domain['title']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="time">Temps (en minutes) :</label>
            <input type="number" name="time" id="time" min="1" required><br><br>

            <label for="difficulty">Difficulté :</label>
            <select name="difficulty" id="difficulty" required>
                <option value="1">Facile</option>
                <option value="2">Moyen</option>
                <option value="3">Difficile</option>
            </select><br><br>

            <button type="submit" name="create">Créer le Niveau</button>
        </form>
    </div>
</body>
</html>
