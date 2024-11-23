<?php
session_start();
session_destroy();
header("Location: index.php"); // Çıkış yapınca giriş ekranına yönlendirin
exit;
?>
