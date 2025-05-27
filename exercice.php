<?php
$domaine = urldecode($_GET['domaine'] ?? '');
$niveau = $_GET['niveau'] ?? '';
if (!$domaine || !$niveau) {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercice</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <link rel="stylesheet" href="assets/styles/css/home-button.css">
    <link rel="stylesheet" href="assets/styles/css/header.css">
    <link rel="stylesheet" href="assets/styles/css/rectangular-button.css">
    <style>
        :root {
            --domain-color: #ccc;
        }
        .header {
            background-color: var(--domain-color, #ff0f00);
        }
        .rectangular-button {
            background-color: var(--domain-color, #ff0f00);
        }
    </style>
</head>
<body class="page-exercice">
    <div class="header">
        <div class="top-header"></div>
        <div class="bottom-header">
            <div class="header-exercice">
                <div class="progress-bar-container">
                    <div class="progress-bar-fill"></div>
                </div>
            </div>
            <div class="grid-header-levels">
                <div class="title-subtitle">
                    <h1 class="header-title" id="header-title"></h1>
                    <h4 class="header-subtitle exercice-title"></h4>
                </div>
                <div class="rectangular-button back-to-home" onclick="window.location.href='home.php'">
                    <img src="assets/img/icons/close-x.png">
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <h2 class="exercice-description"></h2>
        <div class="exercice-options"></div>
        <div class="feedback"></div>
        <button class="next-button" style="display: none;">Suivant</button>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script>
        const domaine = "<?= htmlspecialchars($domaine) ?>";
        const niveau = "<?= htmlspecialchars($niveau) ?>";

        let currentIndex = 0;
        let exercicesData = [];

        function updateProgress() {
            const percentage = ((currentIndex + 1) / exercicesData.length) * 100;
            $('.progress-bar-fill').css('width', percentage + '%');
        }

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }

        function afficherExercice(index) {
            const exercice = exercicesData[index];

            $('.exercice-title').text(exercice.titre);
            $('.exercice-description').text(exercice.description);
            $('.exercice-options').empty();
            $('.feedback').empty();
            $('.next-button').hide();

            shuffleArray(exercice.options);

            exercice.options.forEach(option => {
                const btn = $('<button class="option-button">')
                    .append(
                        $('<div class="rectangular-choice">').append($('<span>').text(option.texte))
                    )
                    .on('click', function () {
                        if ($(this).hasClass('clicked')) return;
                        $(this).addClass('clicked');
                        $(this).find('.rectangular-choice').addClass(option.correcte ? 'bonne-reponse' : 'mauvaise-reponse');
                        $('.feedback').text(option.feedback);
                        if (option.correcte) {
                            $('.option-button').prop('disabled', true);
                            $('.next-button').fadeIn();
                        }
                    });

                $('.exercice-options').append(btn);
            });

            updateProgress();
        }

        $(document).ready(function () {
            fetch('assets/json/exercices_data.json')
                .then(res => res.json())
                .then(data => {
                    console.log('Données chargées :', data);
                    const domaineData = data[domaine];
                    console.log('Domaine extrait :', domaine);
                    console.log('Domaine trouvé :', domaineData);

                    if (!domaineData || !domaineData.niveaux || !domaineData.niveaux[niveau]) {
                        alert("Ce domaine ou niveau n'existe pas dans le fichier JSON.");
                        return;
                    }

                    const color = domaineData.color || '#ff0f00';
                    document.documentElement.style.setProperty('--domain-color', color);
                    document.getElementById('header-title').textContent = domaine.charAt(0).toUpperCase() + domaine.slice(1);

                    exercicesData = domaineData.niveaux[niveau].exercices;
                    afficherExercice(currentIndex);
                });

            $('.next-button').on('click', function () {
                currentIndex++;
                if (currentIndex < exercicesData.length) {
                    afficherExercice(currentIndex);
                } else {
                    window.location.href = 'home.php';
                }
            });
        });
    </script>
</body>
</html>
