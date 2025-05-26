<?php
include 'db.php';

$domain_id = intval($_GET['domain_id'] ?? 0);
$result = $conn->prepare("SELECT id, title FROM levels WHERE fk_domain_id = ?");
$result->bind_param("i", $domain_id);
$result->execute();
$res = $result->get_result();

$levels = [];
while ($row = $res->fetch_assoc()) {
    $levels[] = $row;
}

header('Content-Type: application/json');
echo json_encode($levels);
