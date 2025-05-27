<?php
$domaine = $_GET['domaine'] ?? '';
if (!$domaine) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeAware - Niveaux</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <link rel="stylesheet" href="assets/styles/css/home-button.css">
    <link rel="stylesheet" href="assets/styles/css/header.css">
    <link rel="stylesheet" href="assets/styles/css/rectangular-button.css">
    <style>
        :root {
            --domain-color: #ff0f00; /* fallback au cas où JS échoue */
        }
        .header {
            background-color: var(--domain-color);
        }
        .rectangular-button {
            background-color: var(--domain-color);
        }
    </style>
</head>
<body class="page-levels">
    <div class="header">
        <div class="top-header"></div>
        <div class="bottom-header">
            <div class="grid-header-levels">
                <div class="title-subtitle">
                    <h1 class="header-title" id="header-title"></h1>
                    <h4 class="header-subtitle">Choisissez un thème</h4>
                </div>
                <div class="rectangular-button back-to-home">
                    <img src="assets/img/icons/close-x.png" alt="Retour">
                </div>
            </div>
        </div>
    </div>

    <div class="template-level" id="level-template" style="display: none;">
        <div class="grid-level-buttons">
            <div class="diamond-button home-button">
                <span>1</span>
            </div>
            <div class="description">
                <h2>...</h2>
            </div>
        </div>
        <div class="rectangular-box-levels">
            <div class="grid-rectangular-box-level" data-niveau="" data-domaine="">
                <img class="chrono" src="">
                <img class="diff" src="">
                <img class="play-button" src="" alt="Commencer">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script>
        document.querySelector('.back-to-home').addEventListener('click', () => {
            window.location.href = 'home.php';
        });

        const domaine = "<?= htmlspecialchars($domaine) ?>";

        fetch('assets/json/exercices_data.json')
            .then(res => res.json())
            .then(data => {
                const domaineData = data[domaine];
                if (!domaineData) return;

                document.documentElement.style.setProperty('--domain-color', domaineData.color);
                document.getElementById('header-title').textContent = domaine.charAt(0).toUpperCase() + domaine.slice(1);

                const niveaux = domaineData.niveaux;
                const template = document.getElementById('level-template');
                let index = 0;

                for (const niveauKey in niveaux) {
                    index++;
                    const info = niveaux[niveauKey];
                    const clone = template.cloneNode(true);
                    clone.removeAttribute('id');
                    clone.style.display = '';

                    clone.querySelector('.diamond-button span').textContent = index;
                    clone.querySelector('h2').textContent = info.titre;
                    const spans = clone.querySelectorAll('.grid-rectangular-box-level span');
                    spans[0].textContent = info.temps;
                    spans[1].textContent = info.difficulte;

                    let chronoLevel = "1third";
                    const tempsNum = parseInt(info.temps);
                    if (tempsNum >= 6) chronoLevel = "3third";
                    else if (tempsNum >= 4) chronoLevel = "2third";

                    let diffLevel = "1third";
                    if (info.difficulte === "moyen") diffLevel = "2third";
                    else if (info.difficulte === "difficile") diffLevel = "3third";

                    clone.querySelector('img.chrono').src = `assets/img/icons/levels-${domaine}/${domaine}-chrono-${chronoLevel}.png`;
                    clone.querySelector('img.diff').src = `assets/img/icons/levels-${domaine}/${domaine}-diff-${diffLevel}.png`;
                    const playBtn = clone.querySelector('img.play-button');
                    playBtn.src = `assets/img/icons/levels-${domaine}/play-${domaine}.png`;

                    const gridBox = clone.querySelector('.grid-rectangular-box-level');
                    gridBox.setAttribute('data-niveau', niveauKey);
                    gridBox.setAttribute('data-domaine', domaine);

                    clone.querySelector('.rectangular-box-levels').style.display = 'none';

                    template.after(clone);
                }
            });

        document.addEventListener('click', e => {
            if (e.target.closest('.grid-level-buttons')) {
                const box = e.target.closest('.template-level').querySelector('.rectangular-box-levels');
                document.querySelectorAll('.rectangular-box-levels').forEach(el => {
                    if (el !== box) el.style.display = 'none';
                });
                box.style.display = box.style.display === 'none' ? 'block' : 'none';
            }

            if (e.target.closest('.grid-rectangular-box-level')) {
                const el = e.target.closest('.grid-rectangular-box-level');
                const niveau = el.getAttribute('data-niveau');
                const domaine = el.getAttribute('data-domaine');
                if (niveau && domaine) {
                    window.location.href = `exercice.php?domaine=${domaine}&niveau=${niveau}`;
                }
            }
        });
    </script>
</body>
</html>