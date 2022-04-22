<?php
echo("Login out...");
session_start();

// remove authorization
unset($_SESSION["authorization"]);

header("Location: /login.php");
exit();
