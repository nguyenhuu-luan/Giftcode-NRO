<?php
include 'conn.php'; // Include the database connection

// Xử lý tìm kiếm
$search = "";
$where_clause = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Lấy từ khóa và xử lý ký tự đặc biệt để tránh lỗi SQL
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    // Tạo câu điều kiện lọc: Tìm theo Tên HOẶC theo ID Icon
    // Lưu ý: Tôi đang dùng tên cột 'name' và 'icon_id' như bạn đã xác nhận trước đó.
    $where_clause = " WHERE name LIKE '%$search%' OR icon_id LIKE '%$search%'";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item List</title>
    <style>
        body { font-family: sans-serif; position: relative; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; }
        img { max-width: 40px; max-height: 40px; display: block; }
        .notice { background-color: #fffbe6; border: 1px solid #ffe58f; padding: 10px; margin-bottom: 20px; }

        /* Style cho nút quay lại ở góc trên phải */
        .back-link {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .back-link:hover { background-color: #0056b3; }

        /* Style cho thanh tìm kiếm */
        .search-box { margin-bottom: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px; margin-top: 20px; }
        .search-box input[type="text"] { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        .search-box button { padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .search-box button:hover { background-color: #218838; }
        .search-box a button { background-color: #6c757d; margin-left: 5px; }
        .search-box a button:hover { background-color: #5a6268; }
    </style>
</head>
<body>

<a href="index.php" class="back-link">Quay lại trang tạo GiftCode</a>

<h2>Danh Sách Vật Phẩm (Item List)</h2>

<div class="search-box">
    <form method="GET" action="list-items.php">
        <input type="text" name="search" placeholder="Nhập tên item hoặc ID icon (vd: 390)..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Tìm kiếm</button>
        <?php if (!empty($search)): ?>
            <a href="list-items.php"><button type="button">Xóa lọc</button></a>
        <?php endif; ?>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Icon</th>
        <th>Name</th>
        <th>Description</th>
    </tr>
    <?php
    // Câu truy vấn có thêm biến $where_clause (nếu có tìm kiếm thì biến này sẽ chứa điều kiện, nếu không thì rỗng)
    // Đã sửa tên database thành linhthuydanhbac_data
    $query = "SELECT id, name, description, icon_id FROM `linhthuydanhbac_data`.`item_template`" . $where_clause;

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_assoc($result)) {
            // Đường dẫn ảnh icon
            $icon_path = 'icon/x4/' . $item['icon_id'] . '.png';

            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['id']) . "</td>";
            // Hiển thị ảnh
            echo "<td><img src='" . htmlspecialchars($icon_path) . "' alt='" . htmlspecialchars($item['name']) . "' onerror=\"this.style.display='none'\"></td>";
            echo "<td>" . htmlspecialchars($item['name']) . "</td>";
            // Hiển thị mô tả
            echo "<td>" . htmlspecialchars(@$item['description']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Không tìm thấy vật phẩm nào phù hợp.</td></tr>";
    }

    mysqli_close($conn);
    ?>
</table>

</body>
</html>
