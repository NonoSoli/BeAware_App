<?php
session_start();
include 'admin/db.php'; // adapte le chemin si nécessaire
// Récupération des domaines actifs
$domains = $conn->query("SELECT * FROM domains WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Be Aware</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <!--PWA-->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/img/icons/icon-96x96.png">
    <meta name="apple-mobile-web-app-status-bar" content="white">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="white">
</head>
<script src="js/app.js"></script>
<script src="js/home-buttons.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('serviceWorker.js')
        .then((reg) => {
        console.log("SW enregistré:", reg);

        reg.onupdatefound = () => {
            const newWorker = reg.installing;
            newWorker.onstatechange = () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                console.log("Nouvelle version dispo, rechargement...");
                window.location.reload(); // force le reload dès que le nouveau SW est prêt
            }
            };
        };
        })
        .catch((err) => console.error("Erreur SW:", err));
    }
</script>
<body>
    <div class="header">
        <div class="top-header">
            <div class="logo-container">
                <img class="logo-header" src="assets/img/logo/BeAware_Logo.png" alt="BeAware">
            </div>
        </div>
        <div class="bottom-header">
            <h1 class="header-title">Domaines</h1>
            <h4 class="header-subtitle">Choisissez un domaine</h4>
        </div>
    </div>
    <div class="grid-home-buttons">
        <?php foreach ($domains as $domain): ?>
            <div class="grid-item">
                <button class="<?= strtolower(str_replace([' ', '-', '_'], '-', $domain['title'])) ?>-home-button">
                    <img src="<?= htmlspecialchars($domain['icon_path']) ?>" alt="<?= htmlspecialchars($domain['title']) ?>"/>
                </button>
            </div>
        <?php endforeach; ?>

        <?php foreach ($domains as $domain): ?>
            <div class="grid-item description"><?= htmlspecialchars($domain['title']) ?></div>
        <?php endforeach; ?>
    </div>
</body>
</html>