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
            background-color: var(--domain-color);
        }
        .rectangular-button {
            background-color: var(--domain-color);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

            // Créer une copie des options pour ne pas modifier l'original
            const optionsShuffled = [...exercice.options];
            shuffleArray(optionsShuffled);

            optionsShuffled.forEach(option => {
                const btn = $('<button class="option-button">')
                    .append($('<div class="rectangular-choice">').append($('<span>').text(option.texte)))
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
            console.log('Domaine reçu:', domaine);
            console.log('Niveau reçu:', niveau);

            fetch('assets/json/exercices_data.json?v=' + Date.now())
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Erreur lors du chargement du fichier JSON');
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Données JSON chargées:', data);
                    console.log('Domaines disponibles:', Object.keys(data));

                    // Utiliser directement le domaine sans transformation
                    let domaineData = data[domaine];
                    
                    // Si le domaine exact n'est pas trouvé, essayer avec une recherche flexible
                    if (!domaineData) {
                        console.log('Domaine exact non trouvé, recherche flexible...');
                        for (const key in data) {
                            if (key.toLowerCase() === domaine.toLowerCase()) {
                                domaineData = data[key];
                                console.log('Domaine trouvé avec recherche flexible:', key);
                                break;
                            }
                        }
                    }

                    if (!domaineData) {
                        console.error('Domaine non trouvé:', domaine);
                        alert(`Le domaine "${domaine}" n'existe pas dans le fichier JSON.\nDomaines disponibles: ${Object.keys(data).join(', ')}`);
                        return;
                    }

                    if (!domaineData.niveaux) {
                        console.error('Pas de niveaux pour ce domaine:', domaine);
                        alert(`Aucun niveau défini pour le domaine "${domaine}".`);
                        return;
                    }

                    if (!domaineData.niveaux[niveau]) {
                        console.error('Niveau non trouvé:', niveau);
                        console.log('Niveaux disponibles:', Object.keys(domaineData.niveaux));
                        alert(`Le niveau "${niveau}" n'existe pas pour le domaine "${domaine}".\nNiveaux disponibles: ${Object.keys(domaineData.niveaux).join(', ')}`);
                        return;
                    }

                    // Appliquer la couleur du domaine
                    const color = domaineData.color || '#ff0f00';
                    document.documentElement.style.setProperty('--domain-color', color);
                    document.getElementById('header-title').textContent = domaine.charAt(0).toUpperCase() + domaine.slice(1);

                    // Récupérer les exercices
                    exercicesData = domaineData.niveaux[niveau].exercices;
                    console.log('Exercices trouvés:', exercicesData);

                    if (!exercicesData || exercicesData.length === 0) {
                        $('.main-container').html('<p style="text-align:center">Aucun exercice disponible pour ce niveau.</p>');
                        return;
                    }

                    // Commencer le premier exercice
                    afficherExercice(currentIndex);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données:', error);
                    alert('Erreur lors du chargement des exercices. Vérifiez la console pour plus de détails.');
                });

            $('.next-button').on('click', function () {
                currentIndex++;
                if (currentIndex < exercicesData.length) {
                    afficherExercice(currentIndex);
                } else {
                    alert('Exercices terminés !');
                    window.location.href = 'home.php';
                }
            });
        });
    </script>
</body>
</html>