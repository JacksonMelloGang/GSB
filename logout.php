<?php
echo("Login out...");
session_start();

// remove authorization
unset($_SESSION["authorization"]);

header("Location: /index.php");
exit();
