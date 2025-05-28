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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BeAware - Niveaux</title>
  <link rel="stylesheet" href="assets/styles/css/main.css" />
  <link rel="stylesheet" href="assets/styles/css/home-button.css" />
  <link rel="stylesheet" href="assets/styles/css/header.css" />
  <link rel="stylesheet" href="assets/styles/css/rectangular-button.css" />
  <style>
    :root {
      --domain-color: #ff0f00;
    }

    .header {
      background-color: var(--domain-color);
    }

    .rectangular-button {
      background-color: var(--domain-color);
    }

    .play-button {
      background: none;
      border: none;
      cursor: pointer;
      width: 11em;
      height: 11em;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: auto;
      position: relative;
      z-index: 10;
      padding: 0;
    }

    .play-button svg {
      width: 100%;
      height: 100%;
      fill: var(--domain-color);
      transition: transform 0.2s ease;
    }

    .play-button:hover svg {
      transform: scale(1.1);
    }

    .chrono {
      position: relative;
      width: 100px;
      height: 100px;
      margin: auto;
    }

    .chrono img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      z-index: 1;
    }

    .chrono-circle {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 90%;
      height: 90%;
      transform: translate(-50%, -50%);
      border-radius: 50%;
      background: transparent;
      z-index: 2;
      pointer-events: none;
    }

    .difficulty-indicator {
        display: flex;
        gap: 5px;
        justify-content: center;
        align-items: center;
        margin-top: 5px;
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
          <img src="assets/img/icons/close-x.png" alt="Retour" />
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
        <div class="chrono">
          <div class="chrono-circle"></div>
          <img src="assets/img/icons/chrono-outline.png" alt="Chrono" />
        </div>
        <div class="difficulty-indicator"></div>
        <button class="play-button" title="Commencer">
          <svg viewBox="25 20 50 60" xmlns="http://www.w3.org/2000/svg">
            <polygon points="25,20 75,50 25,80" />
          </svg>
        </button>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    document.querySelector('.back-to-home').addEventListener('click', () => {
      window.location.href = 'home.php';
    });

    const domaine = "<?= htmlspecialchars($domaine) ?>";

    fetch('assets/json/exercices_data.json?v=' + Date.now())
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

            const difficultyIndicator = clone.querySelector('.difficulty-indicator');

            let level = 0;
            switch (info.difficulte) {
            case 'Facile':
                level = 1;
                break;
            case 'Intermédiaire':
                level = 2;
                break;
            case 'Expert':
                level = 3;
                break;
            default:
                level = 0;
            }

            for (let i = 0; i < 3; i++) {
                const diamond = document.createElement('div');
                diamond.classList.add('diamond-button');
                if (i < level) {
                    diamond.classList.add('actif');
                }
                difficultyIndicator.appendChild(diamond);
            }

            const chronoCircle = clone.querySelector('.chrono-circle');
            const temps = parseFloat(info.temps); // Assure-toi que c'est bien un nombre

            let angle = 0;

            if (temps <= 3) {
            angle = 120;
            } else if (temps > 3 && temps <= 4) {
            angle = 240;
            } else if (temps >= 5) {
            angle = 300;
            }

            chronoCircle.style.background = `conic-gradient(
            var(--domain-color) 0deg ${angle}deg,
            transparent ${angle}deg 360deg
            )`;

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
