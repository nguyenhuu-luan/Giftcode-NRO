<?php
include 'conn.php';

$m = mysqli_query($conn, "SELECT * FROM `linhthuydanhbac_data`.`item_option_template`");
$options = [];
if (mysqli_num_rows($m) != 0) {
    while ($varm = mysqli_fetch_array($m)) {
        $options[] = [
            'id' => $varm["id"],
            'name' => $varm["id"] . " - " . $varm["NAME"]
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($options);