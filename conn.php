<?php
//Database Server (localhost)
define('DB_HOST', 'localhost');

//Database User (root)
define('DB_USER', 'root');

//Database Password (null)
define('DB_PASS', '');

//Database Name
define('DB_NAME', 'linhthuydanhbac_acc');


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("NGUYENDUCVUCMS KẾT NỐI: THÔNG TIN KẾT NỐI CƠ SỞ DỮ LIỆU SAI");
@mysqli_set_charset($conn, "utf8");