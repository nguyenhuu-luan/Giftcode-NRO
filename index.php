<?php
include 'conn.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Insert Data GiftCode</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.1/jquery.form.min.js"></script>
    <style>
        .nav-button { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
        .nav-button.blue { background-color: #007bff; }
    </style>
</head>

<body>
    <a href="list-items.php" class="nav-button">Xem Danh Sách Vật Phẩm</a>
    <a href="add-shop-item.php" class="nav-button blue">Thêm Vật Phẩm Vào Shop</a>

    <h2>Insert Data GiftCode</h2>

    <form method="post" action="#" id="ducvupro_giftcode">
        Code: <input type="text" name="code"><br>
        Count Left: <input type="text" name="countLeft"><br>
        Date Expired:
        <select name="dateExpired" id="dateExpiredSelect">
            <option value="1">1 day</option>
            <option value="2">2 days</option>
            <option value="7">1 week</option>
            <option value="30">1 month</option>
            <option value="365">1 year</option>
        </select><br>

        <div id="detailFields">

        </div>
        <button type="button" id="addDetailButton">Add Detail</button><br>

        <input type="submit" value="Insert Data">
    </form>



    <script>
        $(document).ready(function () {
            $(document).on("click", ".add-option-btn", function () {
                var detailField = $(this).closest(".detail-field");
                var optionFields = detailField.find(".option-fields");

                var optionDiv = $('<div></div>');
                optionDiv.addClass("option-field");


                $.ajax({
                    url: 'get-options.php',
                    dataType: 'json',
                    success: function (options) {
                        var selectOptions = '';
                        options.forEach(function (option) {
                            selectOptions += '<option value="' + option.id + '">' + option.name + '</option>';
                        });

                        optionDiv.html('Option Name:  <select name="detailOption[' + detailField.index() + '][optionId][]">' + selectOptions + '</select> Param: <input type="text" name="detailOption[' + detailField.index() + '][optionParam][]"> <button type="button" class="remove-option-btn">Remove Option</button><br>');
                        optionFields.append(optionDiv);
                    }
                });



            });

            $(document).on("click", ".remove-option-btn", function () {
                $(this).closest(".option-field").remove();
            });

            $("#addDetailButton").click(function () {
                var detailDiv = $('<div></div>');
                detailDiv.addClass("detail-field");
                var detailIndex = $(".detail-field").length;

                $.ajax({
                    url: 'get-details.php',
                    dataType: 'json',
                    success: function (options) {
                        var selectOptions = '';
                        options.forEach(function (option) {
                            selectOptions += '<option value="' + option.id + '">' + option.name + '</option>';
                        });

                        detailDiv.html('Detail Name:  <select name="detailId[]">' + selectOptions + '</select> Quantity: <input type="text" name="detailQuantity[]"><br> Options: <div class="option-fields"><div class="option-field"></div></div><button type="button" class="add-option-btn">Add Option</button> <button type="button" class="remove-detail-btn">Remove Detail</button><br>');
                        $("#detailFields").append(detailDiv);
                    }
                });
            });

            $(document).on("click", ".remove-detail-btn", function () {
                $(this).closest(".detail-field").remove();
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $("#ducvupro_giftcode").ajaxForm({
                dataType: 'json',
                url: 'add-giftcode.php',
                beforeSubmit: function () {
                    $("#xuly_dangnhap").show();
                },
                success: function (data) {
                    if (data.code == 0) {
                        $("#ducvupro_giftcode").resetForm();
                        alert(data.msg);
                    } else {
                        alert(data.msg);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                }
            });
        });
    </script>

</body>

</html>