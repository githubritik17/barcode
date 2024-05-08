<?php
session_start();
include_once 'include/connection.php';

// Fetch the last submitted entry
$query = "SELECT * FROM kits_issue ORDER BY id DESC LIMIT 1";
$result = mysqli_query($con, $query);
$entry = mysqli_fetch_assoc($result);

if (!$entry) {
    echo "No entries found.";
    exit();
}

// Fetch all added products corresponding to the last submitted entry's challan_no
$challan_no = $entry['challan_no'];
$product_query = "SELECT * FROM kits_issue WHERE challan_no = '$challan_no'";
$product_result = mysqli_query($con, $product_query);

// Fetch the stitcher name for the invoice
$stitcher_query = "SELECT stitcher_name FROM kits_issue WHERE challan_no = '$challan_no' LIMIT 1";
$stitcher_result = mysqli_query($con, $stitcher_query);
$stitcher_row = mysqli_fetch_assoc($stitcher_result);
$stitcher_name = $stitcher_row['stitcher_name'];

// Fetch the stitcher contact for the invoice
$stitcher_contact_query = "SELECT stitcher_contact FROM stitcher WHERE stitcher_name = '$stitcher_name' LIMIT 1";
$stitcher_contact_result = mysqli_query($con, $stitcher_contact_query);
$stitcher_contact_row = mysqli_fetch_assoc($stitcher_contact_result);
$stitcher_contact = $stitcher_contact_row['stitcher_contact'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khanna Sports Kits Issue Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            background-color: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .heading {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .table {
            margin-top: 0px;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 8.2rem;
            align-items: flex-end;
            color: #555;
        }
        .receiver-signature,
        .issuer-signature {
            flex: 1;
            text-align: left;
        }
        .middle-signature {
            flex: 1;
            text-align: center;
        }
        .print-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
        #head_details{
            display: flex;
            margin-top: 0px;
            padding-top: 0px;
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
        @media print {
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-header">
            <div>
                <h2 class="heading">KHANNA SPORTS KITS ISSUE SLIP</h2>
                <p> Sports Complex, A-7,Delhi Road, Phase 1,<br/> Industrial Area, Mohkam Pur, Meerut,<br/> Uttar Pradesh 250002 (India)</p>
                <p>Contact : 8449441387,98378427750</p>
            </div>
            <div id="head_details">
                <div>
                <p>Stitcher : <?php echo $stitcher_name; ?></p>
                <p>Stitcher Contact : <?php echo $stitcher_contact; ?></p>
                </div>
                <div>
                <p><br/><br/>Challan No : <?php echo $entry['challan_no']; ?></p>
                <p>Date : <?php echo date("d-m-Y"); ?></p>
                </div>
               
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Product Base</th>
                            <th>Product Color</th>
                            <th>Product Quantity</th>
                            <th>Bladder Type</th>
                            <th>Bladder Quantity</th>
                            <th>Thread Type</th>
                            <th>Thread Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($product_result)) : ?>
                            <tr>
                                <td><?php echo $product['product_name']; ?></td>
                                <td><?php echo $product['product_base']; ?></td>
                                <td><?php echo $product['product_color']; ?></td>
                                <td><?php echo $product['issue_quantity']; ?></td>
                                <td><?php echo $product['bladder_name']; ?></td>
                                <td><?php echo $product['bladder_quantity']; ?></td>
                                <td><?php echo $product['thread_name']; ?></td>
                                <td><?php echo $product['thread_quantity']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer">
            <div class="receiver-signature">Receiver Signature</div>
            <div class="middle-signature">Guard Signature</div>
            <div class="issuer-signature">Issuer Signature</div>
        </div>
     <!-- JavaScript code for fetching challan numbers based on selected stitcher and date range -->
 

<script>
        function fetchChallanNumbers(selectedStitcher, fromDate, toDate) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var challanSelect = document.getElementById("select_challan");
                    var challanNumbers = JSON.parse(this.responseText);
                    challanSelect.innerHTML = "<option value='' selected disabled>Select Issue Challan No</option>";
                    challanNumbers.forEach(function(challan) {
                        var option = document.createElement("option");
                        option.value = challan;
                        option.text = challan;
                        challanSelect.appendChild(option);
                    });
                }
            };
            xhttp.open("GET", "fatch_challan_no_for_kits_issue.php?stitcher=" + selectedStitcher + "&from_date=" + fromDate + "&to_date=" + toDate, true);
            xhttp.send();
        }

        function handleDateRangeChange() {
            var selectedStitcher = document.getElementById("select_stitcher").value;
            var fromDate = document.getElementById("from_date").value;
            var toDate = document.getElementById("to_date").value;
            if (selectedStitcher && fromDate && toDate) {
                fetchChallanNumbers(selectedStitcher, fromDate, toDate);
            }
        }

        document.getElementById("from_date").addEventListener("change", handleDateRangeChange);
        document.getElementById("to_date").addEventListener("change", handleDateRangeChange);

        document.getElementById("select_stitcher").addEventListener("change", function() {
            handleDateRangeChange();
        });
    </script>
</body>
</html>





