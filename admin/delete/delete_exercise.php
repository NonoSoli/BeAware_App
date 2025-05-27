<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Désactivation d'un exercice
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE exercices SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: delete_exercise.php");
    exit();
}

// Récupération des domaines
$domains = $conn->query("SELECT id, title FROM domains WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);

$levels = [];
$exercises = [];

$selected_domain = $_GET['domain_id'] ?? null;
$selected_level = $_GET['level_id'] ?? null;

if ($selected_domain) {
    $stmt = $conn->prepare("SELECT id, title FROM levels WHERE fk_domain_id = ?");
    $stmt->bind_param("i", $selected_domain);
    $stmt->execute();
    $levels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if ($selected_level) {
    $stmt = $conn->prepare("SELECT id, title FROM exercices WHERE fk_level_id = ? AND is_active = 1");
    $stmt->bind_param("i", $selected_level);
    $stmt->execute();
    $exercises = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Récupération des niveaux actifs selon le domaine sélectionné
$levels = [];
if ($selected_domain) {
    $stmt = $conn->prepare("SELECT id, title FROM levels WHERE fk_domain_id = ? AND is_active = 1");
    $stmt->bind_param("i", $selected_domain);
    $stmt->execute();
    $levels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un exercice</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <script>
        function confirmDelete(id, name) {
            if (confirm("Êtes-vous sûr de vouloir supprimer l'exercice : " + name + " ?")) {
                window.location.href = "delete_exercise.php?delete_id=" + id;
            }
        }

        function onDomainChange(select) {
            window.location.href = "delete_exercise.php?domain_id=" + select.value;
        }

        function onLevelChange(select) {
            const domain = document.getElementById('domain_id').value;
            window.location.href = "delete_exercise.php?domain_id=" + domain + "&level_id=" + select.value;
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Supprimer un exercice</h2>

        <form method="get">
            <label for="domain_id">Domaine :</label>
            <select id="domain_id" name="domain_id" onchange="onDomainChange(this)">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($domains as $dom): ?>
                    <option value="<?= $dom['id'] ?>" <?= $selected_domain == $dom['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dom['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($selected_domain): ?>
                <?php if (!empty($levels)): ?>
                    <label for="level_id">Choisir un niveau :</label>
                    <select name="level_id" id="level_id" onchange="onLevelChange(this)">
                        <option value="">-- Sélectionner un niveau --</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level['id'] ?>" <?= $selected_level == $level['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($level['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p><em>Aucun niveau actif pour ce domaine.</em></p>
                <?php endif; ?>
            <?php endif; ?>

        </form>

        <?php if (!empty($exercises)): ?>
            <ul>
                <?php foreach ($exercises as $ex): ?>
                    <li>
                        <?= htmlspecialchars($ex['title']) ?>
                        <button onclick="confirmDelete(<?= $ex['id'] ?>, '<?= addslashes($ex['title']) ?>')">Supprimer</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($selected_level): ?>
            <p>Aucun exercice actif pour ce niveau.</p>
        <?php endif; ?>
    </div>
</body>
</html>
