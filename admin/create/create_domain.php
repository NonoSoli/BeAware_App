<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

$uploadError = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title = $_POST['title'] ?? '';
    $color = $_POST['color'] ?? '';

    // Gérer l'upload de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if ($imageExt !== 'png') {
            $uploadError = "Le fichier doit être un .png";
        } else {
            $uploadDir = '../uploads/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

            $newFileName = uniqid() . ".png";
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmpPath, $destPath)) {
                // Insertion dans la BDD via procédure
                $stmt = $conn->prepare("CALL CreateDomain(?, ?, ?)");
                $stmt->bind_param("sss", $title, $newFileName, $color);
                $stmt->execute();
                $successMessage = "Domaine créé avec succès.";
            } else {
                $uploadError = "Erreur lors du téléchargement de l'image.";
            }
        }
    } else {
        $uploadError = "Aucune image sélectionnée ou erreur de téléchargement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Domaine</title>
    <link rel="stylesheet" href="assets/styles/css/main.css">
</head>
<body>
    <div class="container">
        <h2>Créer un nouveau Domaine</h2>

        <?php if ($uploadError): ?>
            <div class="error"><?= htmlspecialchars($uploadError) ?></div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" required><br><br>

            <label for="color">Couleur :</label>
            <input type="color" id="colorPicker" onchange="syncColor(this.value)">
            <input type="text" name="color" id="colorInput" placeholder="#ffffff" maxlength="7" required><br><br>

            <label for="image">Image (.png) :</label>
            <input type="file" name="image" accept=".png" required><br><br>

            <button type="submit" name="create">Créer le Domaine</button>
        </form>
    </div>

    <script>
        const picker = document.getElementById('colorPicker');
        const input = document.getElementById('colorInput');

        function syncColor(val) {
            input.value = val;
        }

        input.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(input.value)) {
                picker.value = input.value;
            }
        });
    </script>
</body>
</html>
