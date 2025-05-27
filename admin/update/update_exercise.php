<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération des domaines
$domaines = $conn->query("SELECT * FROM domains")->fetch_all(MYSQLI_ASSOC);

$levels = [];
$exercises = [];
$selected_exercise = null;

if (isset($_GET['domain_id'])) {
    $stmt = $conn->prepare("SELECT * FROM levels WHERE fk_domain_id = ?");
    $stmt->bind_param("i", $_GET['domain_id']);
    $stmt->execute();
    $levels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['domain_id']) && empty($levels)) {
    $noLevelsMessage = "Aucun niveau actif pour ce domaine.";
}

if (isset($_GET['level_id'])) {
    $stmt = $conn->prepare("SELECT * FROM exercices WHERE fk_level_id = ?");
    $stmt->bind_param("i", $_GET['level_id']);
    $stmt->execute();
    $exercises = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['exercise_id']) && !empty($_GET['exercise_id'])) {
    $exercise_id = intval($_GET['exercise_id']);
    
    // Charger l'exercice
    $stmt = $conn->prepare("SELECT * FROM exercices WHERE id = ?");
    $stmt->bind_param("i", $exercise_id);
    $stmt->execute();
    $selected_exercise = $stmt->get_result()->fetch_assoc();

    // Charger les options
    if ($selected_exercise) {
        $stmt = $conn->prepare("SELECT * FROM options WHERE fk_exercice_id = ? ORDER BY id");
        $stmt->bind_param("i", $exercise_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $selected_exercise['options'] = $result->fetch_all(MYSQLI_ASSOC);
    }
}

if (isset($_GET['level_id']) && empty($exercises)) {
    $noExercisesMessage = "Aucun exercice actif pour ce niveau.";
}

// Mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ex_id = $_POST['exercise_id'];
    $title = $_POST['title'];
    $situation = $_POST['situation'];
    $fk_level_id = $_POST['fk_level_id'];
    $options = $_POST['option'];
    $feedbacks = $_POST['feedback'];
    $correct = $_POST['correct'];

    // CORRECTION: Utiliser le bon nom de table "exercices"
    $stmt = $conn->prepare("UPDATE exercices SET title = ?, situation = ?, fk_level_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $title, $situation, $fk_level_id, $ex_id);
    $stmt->execute();

    // Supprimer les anciennes options
    $conn->query("DELETE FROM options WHERE fk_exercice_id = $ex_id");

    // Réinsérer les options
    for ($i = 0; $i < count($options); $i++) {
        if (!empty($options[$i])) { // Vérifier que l'option n'est pas vide
            $is_correct = ($i == $correct) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO options (title, fk_exercice_id, feedback, correct) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $options[$i], $ex_id, $feedbacks[$i], $is_correct);
            $stmt->execute();
        }
    }

    header("Location: update_exercise.php?domain_id=" . $_POST['domain_id'] . "&level_id=" . $fk_level_id . "&exercise_id=" . $ex_id);
    exit();
}
?>

<a href="../index.php">Accueil</a>
<h2>Modifier un Exercice</h2>

<form method="get">
    <label>Domaine :</label>
    <select name="domain_id" onchange="this.form.submit()">
        <option value="">--Sélectionner--</option>
        <?php foreach ($domaines as $d): ?>
            <option value="<?= $d['id'] ?>" <?= isset($_GET['domain_id']) && $_GET['domain_id'] == $d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($levels)): ?>
<form method="get">
    <input type="hidden" name="domain_id" value="<?= $_GET['domain_id'] ?>">
    <label>Niveau :</label>
    <select name="level_id" onchange="this.form.submit()">
        <option value="">--Sélectionner--</option>
        <?php foreach ($levels as $lvl): ?>
            <option value="<?= $lvl['id'] ?>" <?= isset($_GET['level_id']) && $_GET['level_id'] == $lvl['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($lvl['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
<?php endif; ?>

<?php if (!empty($noLevelsMessage)): ?>
    <p><em><?= htmlspecialchars($noLevelsMessage) ?></em></p>
<?php endif; ?>


<?php if (!empty($exercises)): ?>
<form method="get">
    <input type="hidden" name="domain_id" value="<?= $_GET['domain_id'] ?>">
    <input type="hidden" name="level_id" value="<?= $_GET['level_id'] ?>">
    <label>Exercice :</label>
    <select name="exercise_id">
        <option value="">--Sélectionner--</option>
        <?php foreach ($exercises as $ex): ?>
            <option value="<?= $ex['id'] ?>" <?= isset($_GET['exercise_id']) && $_GET['exercise_id'] == $ex['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ex['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Charger</button>
</form>
<?php endif; ?>

<?php if (!empty($noExercisesMessage)): ?>
    <p><em><?= htmlspecialchars($noExercisesMessage) ?></em></p>
<?php endif; ?>


<?php if ($selected_exercise): ?>
<form method="post">
    <input type="hidden" name="exercise_id" value="<?= $selected_exercise['id'] ?>">
    <input type="hidden" name="domain_id" value="<?= $_GET['domain_id'] ?>">

    <label>Titre :</label>
    <input type="text" name="title" value="<?= htmlspecialchars($selected_exercise['title']) ?>" required><br><br>

    <label>Mise en situation :</label><br>
    <textarea name="situation" rows="4" cols="50" required><?= htmlspecialchars($selected_exercise['situation']) ?></textarea><br><br>

    <label>Niveau :</label>
    <select name="fk_level_id" required>
        <?php foreach ($levels as $lvl): ?>
            <option value="<?= $lvl['id'] ?>" <?= $lvl['id'] == $selected_exercise['fk_level_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($lvl['title']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <fieldset>
        <legend>Options (2 à 3)</legend>
        <?php 
        // Utiliser les options déjà chargées
        $options = isset($selected_exercise['options']) ? $selected_exercise['options'] : [];
        
        for ($i = 0; $i < 3; $i++): 
            $opt = isset($options[$i]) ? $options[$i] : ['title' => '', 'feedback' => '', 'correct' => 0];
        ?>
            <div>
                <label>Option <?= $i + 1 ?> :</label>
                <input type="text" name="option[]" value="<?= htmlspecialchars($opt['title']) ?>" <?= $i < 2 ? 'required' : '' ?> placeholder="Option <?= $i + 1 ?>">
                
                <input type="text" name="feedback[]" value="<?= htmlspecialchars($opt['feedback']) ?>" <?= $i < 2 ? 'required' : '' ?> placeholder="Feedback">
                
                <label>
                    <input type="radio" name="correct" value="<?= $i ?>" <?= ($opt['correct'] == 1) ? 'checked' : '' ?> <?= $i < 2 ? 'required' : '' ?>> Correcte
                </label>
            </div><br>
        <?php endfor; ?>
    </fieldset>

    <input type="submit" name="update" value="Mettre à jour">
</form>
<?php endif; ?>