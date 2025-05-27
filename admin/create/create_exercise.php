<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

$success = '';
$error = '';

// Récupérer les domaines
$domaines = $conn->query("SELECT id, title FROM domains WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_exercice'])) {
    $title = $_POST['ex_title'] ?? '';
    $situation = $_POST['situation'] ?? '';
    $fk_level_id = intval($_POST['fk_level_id'] ?? 0);


    $options = $_POST['option'] ?? [];
    $feedbacks = $_POST['feedback'] ?? [];
    $correct = intval($_POST['correct'] ?? -1);

    if (!$title || !$fk_level_id || count($options) < 2 || count($options) > 3) {
        $error = "Veuillez remplir tous les champs correctement.";
    } else {
        // Créer l'exercice
        $stmt = $conn->prepare("CALL CreateExercice(?, ?, ?)");
        $stmt->bind_param("ssi", $title, $situation, $fk_level_id);
        $stmt->execute();

        // Récupérer l'ID de l'exercice créé
        $ex_id = $conn->query("SELECT LAST_INSERT_ID() AS id")->fetch_assoc()['id'];

        // Ajouter les options
        for ($i = 0; $i < count($options); $i++) {
            $text = $options[$i];
            $fb = $feedbacks[$i];
            $is_correct = ($i == $correct) ? 1 : 0;

            $stmt = $conn->prepare("CALL CreateOption(?, ?, ?, ?)");
            $stmt->bind_param("sisi", $text, $ex_id, $fb, $is_correct);
            $stmt->execute();
        }

        $success = "Exercice et options ajoutés avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Exercice</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
    <script>
        function fetchLevels(domainId) {
            fetch('../fetch_levels.php?domain_id=' + domainId)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('fk_level_id');
                    select.innerHTML = '<option value="">-- Choisir un niveau --</option>';
                    data.forEach(level => {
                        const option = document.createElement('option');
                        option.value = level.id;
                        option.textContent = level.title;
                        select.appendChild(option);
                    });
                });
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="../index.php">Accueil</a>
        <h2>Créer un Exercice</h2>

        <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <form method="post">
            <label for="domain_id">Domaine :</label>
            <select name="domain_id" id="domain_id" required onchange="fetchLevels(this.value)">
                <option value="">-- Choisir un domaine --</option>
                <?php foreach ($domaines as $dom): ?>
                    <option value="<?= $dom['id'] ?>"><?= htmlspecialchars($dom['title']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="fk_level_id">Niveau :</label>
            <select name="fk_level_id" id="fk_level_id" required></select><br><br>

            <label for="ex_title">Titre de l'exercice :</label>
            <input type="text" name="ex_title" id="ex_title" required><br><br>

            <label for="situation">Mise en situation :</label><br>
            <textarea name="situation" id="situation" rows="4" cols="50" placeholder="Décrivez la situation..." required></textarea><br><br>


            <fieldset>
                <legend>Options (2 à 3)</legend>
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <div>
                        <label>Option <?= $i + 1 ?> :</label>
                        <input type="text" name="option[]" <?= $i < 2 ? 'required' : '' ?> placeholder="Texte de l'option">
                        <input type="text" name="feedback[]" <?= $i < 2 ? 'required' : '' ?> placeholder="Feedback">
                        <label>
                            <input type="radio" name="correct" value="<?= $i ?>"> Correcte
                        </label>
                    </div><br>
                <?php endfor; ?>
            </fieldset>

            <button type="submit" name="create_exercice">Créer l'exercice</button>
        </form>
    </div>
</body>
</html>
