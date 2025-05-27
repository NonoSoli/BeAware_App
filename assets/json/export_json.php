<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo "⛔ Accès interdit.";
    exit;
}

require_once __DIR__ . '/../../admin/db.php'; // Connexion MySQLi (via $conn)

function slugify($text) {
    return strtolower(str_replace([' ', '_'], '-', $text));
}

function mapDifficulty($value) {
    return match((int)$value) {
        1 => 'facile',
        2 => 'moyen',
        3 => 'difficile',
        default => 'non défini'
    };
}

$json = [];

$domainResult = $conn->query("SELECT * FROM domains WHERE is_active = 1");
if (!$domainResult || $domainResult->num_rows === 0) {
    $_SESSION['export_status'] = "⚠️ Aucun domaine actif trouvé.";
    header("Location: ../../admin/index.php");
    exit;
}

while ($domain = $domainResult->fetch_assoc()) {
    $domainKey = slugify($domain['title']);
    $json[$domainKey] = [
        "color" => $domain['color'],
        "icon" => $domain['icon_path'],
        "niveaux" => []
    ];

    $levelQuery = $conn->prepare("SELECT * FROM levels WHERE fk_domain_id = ? AND is_active = 1");
    $levelQuery->bind_param("i", $domain['id']);
    $levelQuery->execute();
    $levelResult = $levelQuery->get_result();

    $i = 1;
    while ($level = $levelResult->fetch_assoc()) {
        $niveauKey = "niveau_$i";

        $json[$domainKey]["niveaux"][$niveauKey] = [
            "titre" => $level['title'],
            "temps" => $level['time'] . " min",
            "difficulte" => mapDifficulty($level['difficulty']),
            "exercices" => []
        ];

        $exoQuery = $conn->prepare("SELECT * FROM exercices WHERE fk_level_id = ? AND is_active = 1");
        $exoQuery->bind_param("i", $level['id']);
        $exoQuery->execute();
        $exoResult = $exoQuery->get_result();

        while ($exo = $exoResult->fetch_assoc()) {
            $optQuery = $conn->prepare("SELECT * FROM options WHERE fk_exercice_id = ?");
            $optQuery->bind_param("i", $exo['id']);
            $optQuery->execute();
            $optResult = $optQuery->get_result();

            $options = [];
            while ($opt = $optResult->fetch_assoc()) {
                $options[] = [
                    "texte" => $opt['title'],
                    "correcte" => (bool)$opt['correct'],
                    "feedback" => $opt['feedback']
                ];
            }

            $json[$domainKey][$niveauKey]["exercices"][] = [
                "titre" => $exo['title'],
                "description" => $exo['situation'],
                "options" => $options
            ];
        }

        $i++;
    }
}

// Écriture du JSON
$outputPath = __DIR__ . '/exercices_data.json';
file_put_contents($outputPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$_SESSION['export_status'] = "✅ Fichier exercices_data.json généré avec succès.";
header("Location: ../../admin/index.php");
exit;
