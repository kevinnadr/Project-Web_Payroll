<?php
require 'config/koneksi.php';

echo "<h2>Debug Info</h2>";

// Cek Waktu MySQL
$stmt = $pdo->query("SELECT NOW() as mysql_time");
$row = $stmt->fetch();
echo "MySQL Time (NOW): " . $row['mysql_time'] . "<br><br>";

// Cek Data User (Hanya kolom reset)
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Email</th><th>Reset Token</th><th>Reset Expiry</th><th>Status vs NOW()</th></tr>";

$stmt = $pdo->query("SELECT id, email, reset_token, reset_expiry, (reset_expiry > NOW()) as is_valid FROM users");
while($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . ($row['reset_token'] ? substr($row['reset_token'], 0, 10) . '...' : 'NULL') . "</td>";
    echo "<td>" . ($row['reset_expiry'] ? $row['reset_expiry'] : 'NULL') . "</td>";
    echo "<td>" . ($row['is_valid'] == 1 ? '<span style="color:green">VALID</span>' : '<span style="color:red">EXPIRED/NULL</span>') . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
