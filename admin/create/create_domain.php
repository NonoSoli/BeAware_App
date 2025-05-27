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
    $title = trim($_POST['title'] ?? '');
    $color = $_POST['color'] ?? '';

    // Validation des données
    if (empty($title)) {
        $uploadError = "Le titre est obligatoire";
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $uploadError = "La couleur doit être au format hexadécimal valide (#rrggbb)";
    } else {
        // Gérer l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            
            // Extensions autorisées
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
            
            if (!in_array($imageExt, $allowedExtensions)) {
                $uploadError = "Le fichier doit être une image (PNG, JPG, JPEG, SVG, WebP)";
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $uploadError = "L'image ne peut pas dépasser 2MB";
            } else {
                // Vérifier que c'est bien une image
                $imageInfo = getimagesize($imageTmpPath);
                if ($imageInfo === false) {
                    $uploadError = "Le fichier n'est pas une image valide";
                } else {
                    $uploadDir = '../assets/img/domains/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Nom de fichier unique avec préfixe
                    $newFileName = 'domain_' . uniqid() . '.' . $imageExt;
                    $destPath = $uploadDir . $newFileName;
                    $relativePath = 'assets/img/domains/' . $newFileName;

                    if (move_uploaded_file($imageTmpPath, $destPath)) {
                        try {
                            // Insertion dans la BDD via procédure mise à jour
                            $stmt = $conn->prepare("CALL CreateDomain(?, ?, ?, ?)");
                            $stmt->bind_param("ssss", $title, $relativePath, $color, $imageName);
                            
                            if ($stmt->execute()) {
                                $successMessage = "Domaine '$title' créé avec succès.";
                                // Réinitialiser le formulaire après succès
                                $title = '';
                                $color = '#000000';
                            } else {
                                // Supprimer le fichier si l'insertion échoue
                                if (file_exists($destPath)) {
                                    unlink($destPath);
                                }
                                $uploadError = "Erreur lors de l'enregistrement en base de données : " . $conn->error;
                            }
                        } catch (Exception $e) {
                            // Supprimer le fichier en cas d'erreur
                            if (file_exists($destPath)) {
                                unlink($destPath);
                            }
                            $uploadError = "Erreur lors de la création du domaine : " . $e->getMessage();
                        }
                    } else {
                        $uploadError = "Erreur lors du téléchargement de l'image.";
                    }
                }
            }
        } else {
            // Gestion des erreurs d'upload
            switch ($_FILES['image']['error']) {
                case UPLOAD_ERR_NO_FILE:
                    $uploadError = "Aucune image sélectionnée.";
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $uploadError = "L'image est trop volumineuse.";
                    break;
                default:
                    $uploadError = "Erreur lors du téléchargement de l'image.";
                    break;
            }
        }
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
        <a href="../index.php">&larr; Retour à l'accueil</a>
        <h2>Créer un nouveau Domaine</h2>

        <?php if ($uploadError): ?>
            <div class="error"><?= htmlspecialchars($uploadError) ?></div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre du domaine :</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($title ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="colorInput">Couleur :</label>
                <div class="color-container">
                    <input type="color" id="colorPicker" value="<?= htmlspecialchars($color ?? '#000000') ?>" onchange="syncColor(this.value)">
                    <input type="text" name="color" id="colorInput" placeholder="#000000" maxlength="7" value="<?= htmlspecialchars($color ?? '#000000') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="image">Icône du domaine :</label>
                <input type="file" name="image" id="image" accept=".png,.jpg,.jpeg,.svg,.webp" required>
                <div class="file-info">Formats acceptés : PNG, JPG, JPEG, SVG, WebP - Taille max : 2MB</div>
                
                <div id="preview" class="preview-container">
                    <div class="preview-text">Prévisualisation de l'image</div>
                </div>
            </div>

            <button type="submit" name="create" class="btn-primary">Créer le Domaine</button>
        </form>
    </div>

    <script>
        const picker = document.getElementById('colorPicker');
        const input = document.getElementById('colorInput');
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');

        // Synchronisation des couleurs
        function syncColor(val) {
            input.value = val;
        }

        input.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(input.value)) {
                picker.value = input.value;
            }
        });

        // Prévisualisation de l'image
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Vérifier la taille
                if (file.size > 2 * 1024 * 1024) {
                    preview.innerHTML = '<div class="preview-text" style="color: red;">Image trop volumineuse (max 2MB)</div>';
                    return;
                }
                
                // Vérifier le type
                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    preview.innerHTML = '<div class="preview-text" style="color: red;">Format non supporté</div>';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="preview-text">Prévisualisation de l\'image</div>';
            }
        });

        // Validation du formulaire avant soumission
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const color = document.getElementById('colorInput').value;
            const image = document.getElementById('image').files[0];
            
            if (!title) {
                alert('Le titre est obligatoire');
                e.preventDefault();
                return;
            }
            
            if (!/^#[0-9A-Fa-f]{6}$/.test(color)) {
                alert('La couleur doit être au format hexadécimal (#rrggbb)');
                e.preventDefault();
                return;
            }
            
            if (!image) {
                alert('Veuillez sélectionner une image');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
