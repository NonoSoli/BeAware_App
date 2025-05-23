<?php include 'db.php'; ?>

<h2>Options</h2>

<form method="post">
    <input type="text" name="title" placeholder="Option text" required>
    <input type="number" name="fk_exercice_id" placeholder="Exercice ID" required>
    <input type="text" name="feedback" placeholder="Feedback">
    <label>
        Correct:
        <input type="checkbox" name="correct" value="1">
    </label>
    <input type="submit" name="create" value="Add Option">
</form>

<?php
// CREATE
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $fk_exercice_id = $_POST['fk_exercice_id'];
    $feedback = $_POST['feedback'];
    $correct = isset($_POST['correct']) ? 1 : 0;

    $stmt = $conn->prepare("CALL CreateOption(?, ?, ?, ?)");
    $stmt->bind_param("sisi", $title, $fk_exercice_id, $feedback, $correct);
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("CALL DeleteOption(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// READ
$result = $conn->query("SELECT * FROM options");

echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Exercice ID</th><th>Feedback</th><th>Correct</th><th>Actions</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['title']}</td>
        <td>{$row['fk_exercice_id']}</td>
        <td>{$row['feedback']}</td>
        <td>" . ($row['correct'] ? 'Yes' : 'No') . "</td>
        <td><a href='?delete={$row['id']}'>Delete</a></td>
    </tr>";
}
echo "</table>";
?>
