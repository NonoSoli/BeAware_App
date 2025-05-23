<?php include 'db.php'; ?>

<h2>Domains</h2>

<form method="post">
    <input type="text" name="title" placeholder="Title" required>
    <input type="text" name="color" placeholder="Color">
    <input type="submit" name="create" value="Add Domain">
</form>

<?php
// CREATE
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $color = $_POST['color'];
    $stmt = $conn->prepare("CALL CreateDomain(?, NULL, ?)");
    $stmt->bind_param("ss", $title, $color);
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("CALL DeleteDomain(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// READ
$result = $conn->query("SELECT * FROM domains");

echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Color</th><th>Actions</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['title']}</td>
            <td>{$row['color']}</td>
            <td>
                <a href='?delete={$row['id']}'>Delete</a>
            </td>
          </tr>";
}
echo "</table>";
?>
