<?php
$mysqli = new mysqli("devmysql", "testuser", "testpw", "testdb");

$result = $mysqli->query("SELECT 'Everything working' AS _msg FROM DUAL");
$row = $result->fetch_assoc();
echo $row['_msg'];
?>