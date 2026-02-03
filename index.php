<?php
// File ini tugasnya cuma satu:
// Kalau ada yang buka folder utama, langsung lempar ke halaman Login di folder auth
header("Location: auth/index.php");
exit;
?>