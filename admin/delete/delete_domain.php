<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Traitement de la désactivation
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE domains SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: delete_domain.php");
    exit();
}

// Récupération des domaines actifs
$result = $conn->query("SELECT id, title FROM domains WHERE is_active = 1");
$domains = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un domaine</title>
    <link rel="stylesheet" href="../../assets/styles/css/admin.css">
    <script>
        function confirmDelete(id, name) {
            if (confirm("Êtes-vous sûr de vouloir supprimer le domaine : " + name + " ?")) {
                window.location.href = "delete_domain.php?delete_id=" + id;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Supprimer un domaine</h2>

        <?php if (empty($domains)): ?>
            <p>Aucun domaine actif à supprimer.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($domains as $domain): ?>
                    <li>
                        <?= htmlspecialchars($domain['title']) ?>
                        <button onclick="confirmDelete(<?= $domain['id'] ?>, '<?= addslashes($domain['title']) ?>')">Supprimer</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
