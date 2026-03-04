<?php
session_start();
unset($_SESSION['is_admin']);
header("Location: views/list.php");
exit;