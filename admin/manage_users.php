<?php
session_start();
include "../config/database.php";
include "../includes/header.php";

if ($_SESSION["role"] != "admin") {
    die("Unauthorized access.");
}

$query = "SELECT id, name, email, role, address, contact, gender FROM users";
$result = mysqli_query($conn, $query);
?>

<h2>Manage Users</h2>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Address</th>
            <th>Contact</th>
            <th>Gender</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['contact']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td>
                    <form method="POST" action="delete_user.php">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php include "../includes/footer.php"; ?>
