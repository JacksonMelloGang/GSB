<?php
echo("Login out...");
session_start();
unset($_SESSION["authorization"]);

header("Location: /login.php");
exit();
