<?php
include 'conn.php';

$message = "";

// Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tab_id = isset($_POST['tab_id']) ? intval($_POST['tab_id']) : 0;
    $temp_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : -1;
    $cost = isset($_POST['cost']) ? intval($_POST['cost']) : 0;
    $type_sell = isset($_POST['type_sell']) ? intval($_POST['type_sell']) : 1; // Mặc định
    $is_sell = 1; // Mặc định là bán
    $is_new = 1; // Mặc định là mới

    if ($tab_id > 0 && $temp_id >= 0) {
        // 1. Insert vào bảng item_shop
        // Đã sửa tên database thành linhthuydanhbac_data
        $sql_shop = "INSERT INTO `linhthuydanhbac_data`.`item_shop` (tab_id, temp_id, is_new, is_sell, type_sell, cost, icon_spec, create_time)
                     VALUES ($tab_id, $temp_id, $is_new, $is_sell, $type_sell, $cost, 0, NOW())";

        if (mysqli_query($conn, $sql_shop)) {
            $new_item_shop_id = mysqli_insert_id($conn);

            // 2. Insert các option vào bảng item_shop_option
            if (isset($_POST['option_id']) && is_array($_POST['option_id'])) {
                $option_ids = $_POST['option_id'];
                $params = $_POST['option_param'];

                for ($i = 0; $i < count($option_ids); $i++) {
                    $opt_id = intval($option_ids[$i]);
                    $opt_param = intval($params[$i]);

                    // Chỉ thêm nếu option hợp lệ (có thể là 0)
                    if ($opt_id >= 0) {
                        // Đã sửa tên database thành linhthuydanhbac_data
                        $sql_opt = "INSERT INTO `linhthuydanhbac_data`.`item_shop_option` (item_shop_id, option_id, param)
                                    VALUES ($new_item_shop_id, $opt_id, $opt_param)";
                        mysqli_query($conn, $sql_opt);
                    }
                }
            }
            $message = "<div class='alert success'>Thêm vật phẩm thành công! ID Shop: <strong>$new_item_shop_id</strong></div>";
        } else {
            $message = "<div class='alert error'>Lỗi Database: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='alert error'>Vui lòng chọn Shop, Tab và Vật phẩm hợp lệ.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm Vật Phẩm Vào Shop</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f6f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-primary { background-color: #007bff; }
        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn-secondary { background-color: #6c757d; text-decoration: none; display: inline-block; }

        .option-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; background: #f9f9f9; padding: 10px; border-radius: 4px; }
        .option-select { flex: 2; }
        .option-param { flex: 1; }
        .option-action { flex: 0; }

        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Fix Select2 width */
        .select2-container { width: 100% !important; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn btn-secondary" style="margin-bottom: 15px;">&laquo; Quay lại</a>

    <h2>Thêm Vật Phẩm Vào Shop</h2>

    <?php echo $message; ?>

    <form method="post" action="">
        <div class="form-group">
            <label>Chọn Shop (NPC):</label>
            <select id="shop_select" name="shop_id">
                <option value="">-- Chọn Shop --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Chọn Tab:</label>
            <select id="tab_select" name="tab_id" disabled>
                <option value="">-- Vui lòng chọn Shop trước --</option>
            </select>
        </div>

        <div class="form-group">
            <label>Chọn Vật Phẩm (Item):</label>
            <select id="item_select" name="item_id"></select>
        </div>

        <div class="form-group" style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label>Giá bán:</label>
                <input type="number" name="cost" value="0" required>
            </div>
            <div style="flex: 1;">
                <label>Loại tiền:</label>
                <select name="type_sell">
                    <option value="0">Vàng</option>
                    <option value="1">Ngọc Xanh</option>
                    <option value="2">Hồng Ngọc</option>
                </select>
            </div>
        </div>

        <hr>
        <label>Tùy chọn chỉ số (Options):</label>
        <div id="options_container">
            <!-- Các dòng option sẽ được thêm vào đây -->
        </div>

        <button type="button" id="add_option_btn" class="btn btn-success">+ Thêm Option</button>
        <br><br>

        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 16px;">THÊM VÀO SHOP</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // 1. Load danh sách Shop
    $.ajax({
        url: 'get-shop-data.php?action=get_shops',
        dataType: 'json',
        success: function(data) {
            var options = '<option value="">-- Chọn Shop --</option>';
            $.each(data, function(index, item) {
                options += '<option value="' + item.id + '">' + item.name + '</option>';
            });
            $('#shop_select').html(options);
        }
    });

    // 2. Khi chọn Shop -> Load Tab
    $('#shop_select').change(function() {
        var shopId = $(this).val();
        var tabSelect = $('#tab_select');

        if (shopId) {
            tabSelect.prop('disabled', false).html('<option>Đang tải...</option>');
            $.ajax({
                url: 'get-shop-data.php?action=get_tabs&shop_id=' + shopId,
                dataType: 'json',
                success: function(data) {
                    var options = '';
                    if (data.length === 0) {
                        options = '<option value="">Shop này không có Tab nào</option>';
                    } else {
                        $.each(data, function(index, item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                    }
                    tabSelect.html(options);
                }
            });
        } else {
            tabSelect.prop('disabled', true).html('<option value="">-- Vui lòng chọn Shop trước --</option>');
        }
    });

    // 3. Cấu hình Select2 cho ô chọn Item (Tìm kiếm AJAX)
    $('#item_select').select2({
        placeholder: 'Nhập tên hoặc ID vật phẩm...',
        ajax: {
            url: 'get-shop-data.php?action=get_items',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    // 4. Hàm thêm dòng Option mới
    function addOptionRow() {
        var rowId = Date.now(); // Tạo ID duy nhất
        var html = `
            <div class="option-row" id="row_${rowId}">
                <div class="option-select">
                    <select name="option_id[]" class="select2-option">
                        <option value="">Tìm option...</option>
                    </select>
                </div>
                <div class="option-param">
                    <input type="number" name="option_param[]" placeholder="Chỉ số (Param)" required>
                </div>
                <div class="option-action">
                    <button type="button" class="btn btn-danger remove-option" data-id="${rowId}">X</button>
                </div>
            </div>
        `;
        $('#options_container').append(html);

        // Khởi tạo Select2 cho ô option vừa tạo
        $('#row_' + rowId + ' .select2-option').select2({
            placeholder: 'Chọn chỉ số...',
            ajax: {
                url: 'get-shop-data.php?action=get_options',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });
    }

    // Sự kiện nút Thêm Option
    $('#add_option_btn').click(function() {
        addOptionRow();
    });

    // Sự kiện nút Xóa Option (dùng delegation vì element sinh động)
    $(document).on('click', '.remove-option', function() {
        var id = $(this).data('id');
        $('#row_' + id).remove();
    });

    // Thêm sẵn 1 dòng option lúc đầu
    addOptionRow();
});
</script>

</body>
</html>
