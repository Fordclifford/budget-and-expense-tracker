<?php

ob_start();
session_start();
include("config.php");
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
if ($_SESSION['user_type'] == 'treasurer') {


$error = false;

if (isset($_POST['submit'])) {

 $yr = $_POST['year'];
 $church_id = $_SESSION['church'];
$q1 = "SELECT * FROM bill WHERE financial_year='$yr' AND  church_id = '$church_id'";
$result = mysql_query($q1);

if (mysql_num_rows($result) == 0) {
    $error = TRUE;
    ?>
    <script>
        alert('No bills for the selected financial year \n You Need to add bills first \n You will be redirected to bills page ...');
        window.location.href = 'bills.php';
    </script>
    <?php

}
if (!$error){
        $setSql = "SELECT date, source, amount,mode_of_payment, description from bill WHERE financial_year='$yr' AND church_id = '$church_id' ORDER BY date DESC";
        $setRec = mysql_query($setSql);

        $sumQuery = " SELECT SUM(amount) from bill WHERE financial_year='$yr' AND church_id = '$church_id' ";
        $setSum = mysql_query($sumQuery);


        $columnHeader = '';
        $columnHeader = "Sr NO" . "\t" . "Date" . "\t" . "Source" . "\t" . "Amount" ."\t" . "Mode of Payment" . "\t". "Description" . "\t";

        $setData = '';
        $number = 1;

        while ($rec = mysql_fetch_assoc($setRec)) {

            $rowData = '';
            $num = '"' . $number . '"' . "\t";
            $rowData .= $num;
            foreach ($rec as $value) {
                $value = '"' . $value . '"' . "\t";
                $rowData .= $value;
            }

            $setData .= trim($rowData) . "\n";
            $number++;
        }
        $label = 'Total';
        $blank = '  ';
        while ($rec = mysql_fetch_assoc($setSum)) {

            $rowData = '';
            $blk = "\t" . '"' . $blank . '"' . "\t" . "\t";
            $rowData .= $blk;
            $lbl = '"' . $label . '"' . "\t";
            $rowData .= $lbl;
            foreach ($rec as $value) {
                $value = '"' . $value . '"' . "\t";
                $rowData .= $value;
            }

            $setData .= "\n" . trim($rowData) . "\n";
        }


        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=bills.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo ucwords($columnHeader) . "\n" . $setData . "\n";
}
}
}
?>
