<?php
include_once 'include/connection.php';

if (isset($_GET['product_name']) && isset($_GET['product_base'])) {
    $product_name = $_GET['product_name'];
    

    $product_color_query = "SELECT DISTINCT small_sheet_color FROM sheets_small_stock  WHERE product_name = '$product_name' AND";
    $product_color_result = mysqli_query($con, $product_color_query);

    $colors = array();
    while ($row = mysqli_fetch_assoc($product_color_result)) {
        $colors[] = $row['product_color'];
    }

    echo json_encode($colors);
}
?>