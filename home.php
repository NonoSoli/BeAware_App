<?php
session_start();
include 'admin/db.php';
$domains = $conn->query("SELECT * FROM domains WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);

function hexToRgbaShadow($hexColor, $opacityHex = '33') {
    $hex = ltrim($hexColor, '#');
    if (strlen($hex) === 6) {
        return '#' . $hex . $opacityHex; // ex: #0082FF33
    }
    return '#00000033';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Be Aware</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <link rel="stylesheet" href="assets/styles/css/home-button.css">
    <link rel="stylesheet" href="assets/styles/css/header.css">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/img/icons/icon-96x96.png">
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
                        window.location.reload();
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
            <h1 class="home-header-title">Domaines</h1>
            <h4 class="home-header-subtitle">Choisissez un domaine</h4>
        </div>
    </div>

    <div class="grid-home-buttons">
        <?php for ($i = 0; $i < count($domains); $i += 2): ?>
            <?php for ($j = 0; $j < 2 && $i + $j < count($domains); $j++): ?>
                <?php 
                    $domain = $domains[$i + $j];
                    $color = htmlspecialchars($domain['color']);
                    $shadow = htmlspecialchars(hexToRgbaShadow($domain['color']));
                ?>
                <div class="grid-item">
                    <button 
                        class="home-button"
                        style="--domain-color: <?= $color ?>; --domain-shadow: <?= $shadow ?>;"
                        onclick="window.location.href='levels.php?domaine=<?= strtolower(str_replace([' ', '_'], '-', $domain['title'])) ?>'"
                    >
                        <img src="<?= htmlspecialchars($domain['icon_path']) ?>" alt="<?= htmlspecialchars($domain['title']) ?>"/>
                    </button>
                </div>
            <?php endfor; ?>

            <?php for ($j = 0; $j < 2 && $i + $j < count($domains); $j++): ?>
                <div class="grid-item description"><?= htmlspecialchars($domains[$i + $j]['title']) ?></div>
            <?php endfor; ?>
        <?php endfor; ?>
    </div>
</body>
</html>
