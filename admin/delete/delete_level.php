<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Désactivation du niveau
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE levels SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: delete_level.php");
    exit();
}

// Récupérer tous les domaines actifs
$domains = $conn->query("SELECT id, title FROM domains WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);

// Option de filtre par domaine
$selected_domain = $_GET['domain_id'] ?? null;
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
    <title>Supprimer un niveau</title>
    <link rel="stylesheet" href="../../assets/styles/css/admin.css">
    <script>
        function confirmDelete(id, name) {
            if (confirm("Supprimer le niveau : " + name + " ?")) {
                window.location.href = "delete_level.php?delete_id=" + id;
            }
        }

        function onDomainChange(select) {
            window.location.href = "delete_level.php?domain_id=" + select.value;
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Supprimer un niveau</h2>

        <div class="form-group">
            <label for="domain_id">Filtrer par domaine :</label>
            <select name="domain_id" id="domain_id" onchange="onDomainChange(this)">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($domains as $dom): ?>
                    <option value="<?= $dom['id'] ?>" <?= $selected_domain == $dom['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dom['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($selected_domain && !empty($levels)): ?>
            <ul class="delete-list">
                <?php foreach ($levels as $lvl): ?>
                    <li>
                        <span><?= htmlspecialchars($lvl['title']) ?></span>
                        <button class="btn-delete" onclick="confirmDelete(<?= $lvl['id'] ?>, '<?= addslashes($lvl['title']) ?>')">Supprimer</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($selected_domain): ?>
            <p class="empty-message">Aucun niveau actif pour ce domaine.</p>
        <?php endif; ?>
    </div>

</body>
</html>
