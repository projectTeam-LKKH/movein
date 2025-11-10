<?php
session_start();
session_destroy(); // 세션 전체 종료
header("Location: ../index.php");
exit;
?>
