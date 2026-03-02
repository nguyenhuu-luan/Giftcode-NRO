<?php
include 'conn.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'get_shops') {
    // Lấy danh sách Shop kèm tên NPC
    // Đã sửa tên database thành linhthuydanhbac_data
    $query = "SELECT s.id, s.npc_id, s.tag_name, n.NAME as npc_name
              FROM `linhthuydanhbac_data`.`shop` s
              LEFT JOIN `linhthuydanhbac_data`.`npc_template` n ON s.npc_id = n.id";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id' => $row['id'],
            'name' => $row['npc_name'] . " (" . $row['tag_name'] . ")"
        ];
    }
    echo json_encode($data);
}
elseif ($action == 'get_tabs') {
    // Lấy danh sách Tab theo Shop ID
    $shop_id = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 0;
    // Đã sửa tên database thành linhthuydanhbac_data
    $query = "SELECT id, NAME FROM `linhthuydanhbac_data`.`tab_shop` WHERE shop_id = $shop_id";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Tên tab trong DB có thể chứa ký tự đặc biệt như <>, cần xử lý hiển thị
        $tabName = str_replace("<>", " - ", $row['NAME']);
        $data[] = [
            'id' => $row['id'],
            'name' => $tabName
        ];
    }
    echo json_encode($data);
}
elseif ($action == 'get_items') {
    // Lấy danh sách Item Template (cho Select2)
    $search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
    // Đã sửa tên database thành linhthuydanhbac_data
    $query = "SELECT id, NAME FROM `linhthuydanhbac_data`.`item_template`
              WHERE NAME LIKE '%$search%' OR id LIKE '$search%'
              LIMIT 50";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id' => $row['id'],
            'text' => "[" . $row['id'] . "] " . $row['NAME']
        ];
    }
    echo json_encode(['results' => $data]);
}
elseif ($action == 'get_options') {
    // Lấy danh sách Option Template (cho Select2)
    $search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
    // Đã sửa tên database thành linhthuydanhbac_data
    $query = "SELECT id, NAME FROM `linhthuydanhbac_data`.`item_option_template`
              WHERE NAME LIKE '%$search%' OR id LIKE '$search%'
              LIMIT 50";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Thay thế ký tự # bằng ... để dễ hiểu hơn
        $optName = str_replace("#", "...", $row['NAME']);
        $data[] = [
            'id' => $row['id'],
            'text' => "[" . $row['id'] . "] " . $optName
        ];
    }
    echo json_encode(['results' => $data]);
}
?>