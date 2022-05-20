<?php
echo("Login out...");
session_start();

// delete session
session_destroy();

header("Location: /index.php");
exit();
