<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uploadError = '';
$successMessage = '';

// Récupération des domaines
$domaines = $conn->query("SELECT * FROM domains WHERE is_active = 1");

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
    $title = trim($_POST['title']);
    $color = $_POST['color'];
    $icon_path = null;
    $icon_filename = null;

    // Validation des données
    if (empty($title)) {
        $uploadError = "Le titre est obligatoire";
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $uploadError = "La couleur doit être au format hexadécimal valide (#rrggbb)";
    } else {
        // Gestion de l'upload d'image
        if (!empty($_FILES['icon']['tmp_name']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['icon']['tmp_name'];
            $imageName = basename($_FILES['icon']['name']);
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            
            // Extensions autorisées
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
            
            if (!in_array($imageExt, $allowedExtensions)) {
                $uploadError = "Le fichier doit être une image (PNG, JPG, JPEG, SVG, WebP)";
            } elseif ($_FILES['icon']['size'] > 2 * 1024 * 1024) {
                $uploadError = "L'image ne peut pas dépasser 2MB";
            } else {
                // Vérifier que c'est bien une image
                $imageInfo = getimagesize($imageTmpPath);
                if ($imageInfo === false) {
                    $uploadError = "Le fichier n'est pas une image valide";
                } else {
                    $uploadDir = '../../assets/img/domains/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Supprimer l'ancienne image si elle existe
                    if (!empty($selected_domain['icon_path']) && file_exists('../../' . $selected_domain['icon_path'])) {
                        unlink('../../' . $selected_domain['icon_path']);
                    }

                    // Nom de fichier unique
                    $newFileName = 'domain_' . uniqid() . '.' . $imageExt;
                    $destPath = $uploadDir . $newFileName;
                    $icon_path = 'assets/img/domains/' . $newFileName;
                    $icon_filename = $imageName;

                    if (!move_uploaded_file($imageTmpPath, $destPath)) {
                        $uploadError = "Erreur lors du téléchargement de l'image.";
                    }
                }
            }
        } else {
            // Garder l'image existante si pas de nouvel upload
            $icon_path = $selected_domain['icon_path'];
            $icon_filename = $selected_domain['icon_filename'] ?? '';
        }

        // Si pas d'erreur, procéder à la mise à jour
        if (empty($uploadError)) {
            try {
                // Mise à jour de la procédure stockée
                $stmt = $conn->prepare("CALL UpdateDomain(?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $id, $title, $icon_path, $color, $icon_filename);
                
                if ($stmt->execute()) {
                    $successMessage = "Domaine mis à jour avec succès.";
                    // Recharger les données du domaine
                    $stmt = $conn->prepare("SELECT * FROM domains WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $selected_domain = $result->fetch_assoc();
                } else {
                    $uploadError = "Erreur lors de la mise à jour : " . $conn->error;
                }
            } catch (Exception $e) {
                $uploadError = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un domaine</title>
    <link rel="stylesheet" href="../assets/styles/css/main.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .color-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .color-container input[type="color"] {
            width: 60px;
            height: 40px;
            padding: 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .current-image {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .preview-container {
            margin-top: 10px;
            padding: 10px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
        }
        
        .preview-container img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .error {
            background-color: #fee;
            color: #c00;
            padding: 10px;
            border: 1px solid #fcc;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success {
            background-color: #efe;
            color: #060;
            padding: 10px;
            border: 1px solid #cfc;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php">&larr; Retour à l'accueil</a>
        <h2>Modifier un domaine</h2>

        <?php if ($uploadError): ?>
            <div class="error"><?= htmlspecialchars($uploadError) ?></div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- Sélection du domaine -->
        <form method="get" class="form-group">
            <label for="id">Choisir un domaine :</label>
            <select name="id" onchange="this.form.submit()">
                <option value="">--Sélectionner--</option>
                <?php 
                $domaines->data_seek(0); // Reset du curseur
                while ($row = $domaines->fetch_assoc()) : ?>
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

            <div class="form-group">
                <label for="title">Titre :</label>
                <input type="text" name="title" value="<?= htmlspecialchars($selected_domain['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="color">Couleur :</label>
                <div class="color-container">
                    <input type="color" id="color_picker" value="<?= htmlspecialchars($selected_domain['color']) ?>" oninput="document.getElementById('color_text').value = this.value">
                    <input type="text" id="color_text" name="color" value="<?= htmlspecialchars($selected_domain['color']) ?>" pattern="#[0-9a-fA-F]{6}" maxlength="7" required>
                </div>
            </div>

            <div class="form-group">
                <label>Icône actuelle :</label>
                <?php if (!empty($selected_domain['icon_path']) && file_exists('../../' . $selected_domain['icon_path'])): ?>
                    <img src="../../<?= htmlspecialchars($selected_domain['icon_path']) ?>" alt="Icône actuelle" class="current-image">
                <?php else: ?>
                    <p><em>Aucune image enregistrée ou fichier introuvable.</em></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="icon">Changer l'icône :</label>
                <input type="file" name="icon" id="icon" accept=".png,.jpg,.jpeg,.svg,.webp">
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    Formats acceptés : PNG, JPG, JPEG, SVG, WebP - Taille max : 2MB<br>
                    Laissez vide pour conserver l'image actuelle
                </div>
                
                <div id="preview" class="preview-container" style="display: none;">
                    <div class="preview-text">Nouvelle image</div>
                </div>
            </div>

            <button type="submit" name="update" class="btn-primary">Mettre à jour</button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        // Prévisualisation de l'image
        document.getElementById('icon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                // Vérifier la taille
                if (file.size > 2 * 1024 * 1024) {
                    preview.innerHTML = '<div style="color: red;">Image trop volumineuse (max 2MB)</div>';
                    preview.style.display = 'flex';
                    return;
                }
                
                // Vérifier le type
                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    preview.innerHTML = '<div style="color: red;">Format non supporté</div>';
                    preview.style.display = 'flex';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Nouvelle image">`;
                    preview.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Synchronisation des couleurs
        document.getElementById('color_text').addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                document.getElementById('color_picker').value = this.value;
            }
        });
    </script>
</body>
</html>