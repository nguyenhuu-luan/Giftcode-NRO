<?php
// Bật báo lỗi để dễ dàng theo dõi nếu có vấn đề phát sinh
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form
    $codeText = $_POST["code"];
    $countLeft = (int)$_POST["countLeft"];
    $dateExpired = (int)$_POST["dateExpired"];

    $detailArray = [];
    // Kiểm tra nếu có dữ liệu vật phẩm gửi lên
    if (isset($_POST["detailId"]) && is_array($_POST["detailId"])) {
        for ($i = 0; $i < count($_POST["detailId"]); $i++) {
            $detailId = $_POST["detailId"][$i];
            $detailQuantity = $_POST["detailQuantity"][$i];

            $options = [];
            if (isset($_POST["detailOption"][$i]["optionId"])) {
                for ($j = 0; $j < count($_POST["detailOption"][$i]["optionId"]); $j++) {
                    $optionId = $_POST["detailOption"][$i]["optionId"][$j];
                    $optionParam = $_POST["detailOption"][$i]["optionParam"][$j];
                    $options[] = [
                        "id" => (int)$optionId,
                        "param" => (int)$optionParam
                    ];
                }
            }

            $detailArray[] = [
                "id" => (int)$detailId,
                "quantity" => (int)$detailQuantity,
                "options" => $options
            ];
        }
    }

    // Xử lý chuỗi JSON để không bị lỗi khi gặp dấu ngoặc kép
    $detailJson = mysqli_real_escape_string($conn, json_encode($detailArray));
    $codeClean = mysqli_real_escape_string($conn, $codeText);

    // Câu lệnh SQL đã loại bỏ cột listIdUser bị thiếu trong database của bạn
    $sql = "INSERT INTO `linhthuydanhbac_acc`.`giftcode` SET
        `code` = '$codeClean',
        `count_left` = '$countLeft',
        `detail` = '$detailJson',
        `datecreate` = CURRENT_TIMESTAMP(),
        `expired` = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL $dateExpired DAY)";

    $giftcode = mysqli_query($conn, $sql);

    // Trả về kết quả cho phía Client
    if ($giftcode) {
        $status = 0;
        $msg = "Thêm giftcode thành công!";
    } else {
        $status = 1;
        $msg = "Lỗi MySQL: " . mysqli_error($conn);
    }

    echo json_encode(['code' => $status, 'msg' => $msg]);
}
?>