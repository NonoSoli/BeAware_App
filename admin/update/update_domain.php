<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération des domaines
$domaines = $conn->query("SELECT * FROM domains");

$selected_domain = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM domains WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_domain = $result->fetch_assoc();
}

// Traitement du formulaire
if (isset($_POST['update'])) {
    $id = $_POST['domain_id'];
    $title = $_POST['title'];
    $color = $_POST['color'];

    // Upload d'image
    if (!empty($_FILES['icon']['tmp_name'])) {
        $icon = file_get_contents($_FILES['icon']['tmp_name']);
    } else {
        // Reprendre l'image existante
        $stmt = $conn->prepare("SELECT icon FROM domains WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($icon);
        $stmt->fetch();
    }

    $stmt = $conn->prepare("CALL UpdateDomain(?, ?, ?, ?)");
    $stmt->bind_param("isss", $id, $title, $icon, $color);
    $stmt->execute();

    header("Location: update_domain.php?id=" . $id);
    exit();
}
?>

<h2>Modifier un domaine</h2>

<!-- Sélection du domaine -->
<form method="get">
    <label for="id">Choisir un domaine :</label>
    <select name="id" onchange="this.form.submit()">
        <option value="">--Sélectionner--</option>
        <?php while ($row = $domaines->fetch_assoc()) : ?>
            <option value="<?= $row['id'] ?>" <?= isset($selected_domain) && $selected_domain['id'] == $row['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- Formulaire de mise à jour -->
<?php if ($selected_domain): ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="domain_id" value="<?= $selected_domain['id'] ?>">

    <label for="title">Titre :</label>
    <input type="text" name="title" value="<?= htmlspecialchars($selected_domain['title']) ?>" required><br>

    <label for="color">Couleur :</label>
    <input type="color" name="color_picker" value="<?= htmlspecialchars($selected_domain['color']) ?>" oninput="document.getElementById('color_text').value = this.value">
    <input type="text" id="color_text" name="color" value="<?= htmlspecialchars($selected_domain['color']) ?>" pattern="#[0-9a-fA-F]{6}" maxlength="7" required><br>

    <label>Icône actuelle :</label><br>
    <?php if (!empty($selected_domain['icon'])): ?>
        <img src="data:image/png;base64,<?= base64_encode($selected_domain['icon']) ?>" alt="Icône actuelle" width="100"><br>
    <?php else: ?>
        <em>Aucune image enregistrée.</em><br>
    <?php endif; ?>

    <label for="icon">Changer l'icône (.png) :</label>
    <input type="file" name="icon" accept="image/png"><br><br>

    <input type="submit" name="update" value="Mettre à jour">
</form>
<?php endif; ?>
