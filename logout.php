<?php
echo("Login out...");
session_start();

// remove authorization
unset($_SESSION["authorization"]);
unset($_SESSION["userId"]);

header("Location: /index.php");
exit();
