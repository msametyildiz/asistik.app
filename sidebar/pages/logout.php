<?php
session_start();
session_destroy();
header("Location: ../index.php"); // Çıkış yapınca bir üst klasördeki giriş ekranına yönlendirin
exit;
?>

