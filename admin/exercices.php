<?php include 'db.php'; ?>

<h2>Exercices</h2>

<form method="post">
    <input type="text" name="title" placeholder="Title" required>
    <input type="number" name="fk_level_id" placeholder="Level ID" required>
    <input type="submit" name="create" value="Add Exercice">
</form>

<?php
// CREATE
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $fk_level_id = $_POST['fk_level_id'];

    $stmt = $conn->prepare("CALL CreateExercice(?, ?)");
    $stmt->bind_param("si", $title, $fk_level_id);
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("CALL DeleteExercice(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// READ
$result = $conn->query("SELECT * FROM exercices");

echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Level ID</th><th>Actions</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['title']}</td>
        <td>{$row['fk_level_id']}</td>
        <td><a href='?delete={$row['id']}'>Delete</a></td>
    </tr>";
}
echo "</table>";
?>
