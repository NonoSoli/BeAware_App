<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

$successMessage = '';
$errorMessage = '';

$selectedDomainId = isset($_GET['domain_id']) ? intval($_GET['domain_id']) : null;

$levels = [];
if ($selectedDomainId) {
    $stmt = $conn->prepare("SELECT id, title FROM levels WHERE fk_domain_id = ?");
    $stmt->bind_param("i", $selectedDomainId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $levels[] = $row;
    }
}

// Récupérer les domaines pour le menu déroulant
$domains = [];
$result = $conn->query("SELECT id, title FROM domains WHERE is_active = 1");
while ($row = $result->fetch_assoc()) {
    $domains[] = $row;
}

// Si un niveau est sélectionné
$selectedLevel = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM levels WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedLevel = $result->fetch_assoc();
}

// Mise à jour du niveau
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'] ?? '';
    $fk_domain_id = intval($_POST['fk_domain_id'] ?? 0);
    $time = intval($_POST['time'] ?? 0);
    $difficulty = intval($_POST['difficulty'] ?? 0);

    if ($title && $fk_domain_id && $time && $difficulty) {
        $stmt = $conn->prepare("CALL UpdateLevel(?, ?, ?, ?, ?)");
        $stmt->bind_param("isiii", $id, $title, $fk_domain_id, $time, $difficulty);
        if ($stmt->execute()) {
            $successMessage = "Niveau mis à jour avec succès.";
        } else {
            $errorMessage = "Erreur lors de la mise à jour.";
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
    <title>Modifier un Niveau</title>
    <link rel="stylesheet" href="../../assets/styles/css/admin.css">
</head>
<style>
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-group input, .form-group select {
        width: 100%;
        max-width: 300px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .color-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .color-container input[type="color"] {
        width: 60px;
        height: 40px;
        padding: 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .current-image {
        max-width: 100px;
        max-height: 100px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px 0;
    }
    
    .preview-container {
        margin-top: 10px;
        padding: 10px;
        border: 1px dashed #ccc;
        border-radius: 4px;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9f9f9;
    }
    
    .preview-container img {
        max-width: 100px;
        max-height: 100px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .error {
        background-color: #fee;
        color: #c00;
        padding: 10px;
        border: 1px solid #fcc;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .success {
        background-color: #efe;
        color: #060;
        padding: 10px;
        border: 1px solid #cfc;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Modifier un Niveau</h2>

        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form method="get">
            <label for="domain_id">Filtrer par domaine :</label>
            <select name="domain_id" id="domain_id" onchange="this.form.submit()">
                <option value="">-- Choisir un domaine --</option>
                <?php foreach ($domains as $domain): ?>
                    <option value="<?= $domain['id'] ?>" <?= $selectedDomainId == $domain['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($domain['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($selectedDomainId && !empty($levels)): ?>
                <label for="id">Sélectionner un niveau :</label>
                <select name="id" id="id" onchange="this.form.submit()">
                    <option value="">-- Choisir un niveau --</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?= $level['id'] ?>" <?= isset($selectedLevel['id']) && $selectedLevel['id'] == $level['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($level['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($selectedDomainId): ?>
                <p><em>Aucun niveau actif pour ce domaine.</em></p>
            <?php endif; ?>
        </form>


        <?php if ($selectedLevel): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= $selectedLevel['id'] ?>">

                <label for="title">Titre :</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($selectedLevel['title']) ?>" required><br><br>

                <label for="fk_domain_id">Domaine :</label>
                <select name="fk_domain_id" id="fk_domain_id" required>
                    <?php foreach ($domains as $domain): ?>
                        <option value="<?= $domain['id'] ?>" <?= $domain['id'] == $selectedLevel['fk_domain_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($domain['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="time">Temps (en minutes) :</label>
                <input type="number" name="time" id="time" min="1" value="<?= $selectedLevel['time'] ?>" required><br><br>

                <label for="difficulty">Difficulté :</label>
                <select name="difficulty" id="difficulty" required>
                    <option value="1" <?= $selectedLevel['difficulty'] == 1 ? 'selected' : '' ?>>Facile</option>
                    <option value="2" <?= $selectedLevel['difficulty'] == 2 ? 'selected' : '' ?>>Moyen</option>
                    <option value="3" <?= $selectedLevel['difficulty'] == 3 ? 'selected' : '' ?>>Difficile</option>
                </select><br><br>

                <button type="submit" name="update">Mettre à jour</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
