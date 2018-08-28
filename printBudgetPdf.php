<?Php
ob_start();
session_start();
require_once 'config.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
$res = mysql_query("SELECT * FROM church WHERE id=" . $_SESSION['user']);

$userRow = mysql_fetch_array($res);

$church_id = $_SESSION['user'];
$error = false;

if (isset($_POST['submit'])) {
    $year = $_POST['year'];
    $comment = $_POST['comment'];

       $q1 = "SELECT * FROM income_sources WHERE church_id = '$church_id' AND financial_year =$year";
    $q11 = "SELECT * FROM budget_expenses WHERE church_id = '$church_id' AND financial_year =$year";
    $result = mysql_query($q1);
    $result1 = mysql_query($q11);
    if (mysql_num_rows($result1) == 0) {
        $error= true;
        ?>
        <script>
            alert('You Need to add income and Expenses first \n You will be redirected to expenses page ...');
            window.location.href = 'expenses.php';
        </script>
        <?php
    }
    if (mysql_num_rows($result) == 0) {
        $error=true;
        ?>
        <script>
            alert('You Need to add income and Expenses first \n You will be redirected to income page ...');
            window.location.href = 'income.php';
        </script>
        <?php
    }
    
    if (empty($year)) {
        $errMSG = "Sorry an Error Occured! Check and Try Again!";
        $error = true;
        $yrError = "Please Select Date.";
        $errTyp = "danger";
    }
    if (!file_exists('fpdf.php')) {
        echo " Place fpdf.php file in this directory before using this page. ";
        exit;
    }

    if (!file_exists('font')) {
        echo " Place font directory in this directory before using this page. ";
        exit;
    }
    require "dbconfig.php";
    // connection to database 
    require('fpdf.php');

    class PDF extends FPDF {

// Page header
 function Header()
{
    // Logo
    $this->Image('logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(30,10,'Title',1,0,'C');
    // Line break
    $this->Ln(20);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

    }

    $pdf = new FPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

if(!$error){

    $count = "select source_name,amount from income_sources WHERE church_id = $church_id AND financial_year =$year";
    $totalincome = " select SUM(amount) AS totalIncome from income_sources WHERE church_id = $church_id AND financial_year =$year";
    $expense = "select expense_name, amount from budget_expenses WHERE church_id = $church_id AND financial_year =$year";
    $totalexpense = " select SUM(amount) AS totalExpense from budget_expenses WHERE church_id = $church_id AND financial_year =$year";
    $church = "select name AS name from church WHERE id = $church_id";
    $fyr = mysql_query("Select year AS Fyear from financial_year where church_id =$church_id AND id=$year");
    $y_row = mysql_fetch_assoc($fyr);


// SQL to get  records 
//check if recordes exist
 


    $pdf->Image('assets/image/logo-2x.png', 100, 5, 30, 0, '', '../bextsystem');
    $pdf->Cell(10, 5);
    $pdf->SetFont('Arial', 'B', 8);
    foreach ($dbo->query($church) as $row) {
        $pdf->Ln();
        $pdf->SetLeftMargin(100);
        $pdf->Cell(20, 0, $row['name']);
        $pdf->SetLeftMargin(30);
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetLeftMargin(30);
    $pdf->Cell(0, 20, '' . $y_row['Fyear'] . ' Church Proposed Operating Budget');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 10, 'INCOME SOURCES');
    $pdf->Ln();

    $width_cell = array(100, 50, 40, 30);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->SetFillColor(193, 229, 252); // Background color of header 
// Header starts /// 

    $pdf->Cell($width_cell[0], 10, 'SOURCE NAME', 1, 0, 'C', true); // First header column 
    $pdf->Cell($width_cell[1], 10, 'AMOUNT', 1, 0, 'C', true); // Second header column
    $pdf->Ln();

//// header ends ///////

    $pdf->SetFont('Arial', '', 14);
    $pdf->SetFillColor(235, 236, 236); // Background color of header 
    $fill = false; // to give alternate background fill color to rows 
/// each record is one row  ///
    foreach ($dbo->query($count) as $row) {
        $pdf->Cell($width_cell[0], 10, $row['source_name'], 1, 0, 'C', $fill);
        $pdf->Cell($width_cell[1], 10, $row['amount'], 1, 0, 'L', $fill);

        $pdf->Ln();
        $fill = !$fill; // to give alternate background fill  color to rows
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 16);
//$pdf->SetFillColor(0, 240, 180);
    $label = "TOTAL INCOME";
    foreach ($dbo->query($totalincome) as $row) {

        $pdf->Cell($width_cell[0], 10, $label, 0, 0, 'C', $fill);
        $pdf->Cell($width_cell[1], 10, $row['totalIncome'], 0, 0, 'C', $fill);
        global $inc;
        $inc = $row['totalIncome'];

        // to give alternate background fill  color to rows
    }
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();


    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 10, 'EXPENSES ');
    $pdf->Ln();

/// Expenses///
    $pdf->SetFillColor(193, 229, 252); // Background color of header 
    $pdf->SetFont('Arial', 'B', 15);
// Header starts /// 
    $pdf->Cell($width_cell[0], 10, 'EXPENSE NAME', 1, 0, 'C', true); // First header column 
    $pdf->Cell($width_cell[1], 10, 'AMOUNT', 1, 0, 'C', true); // Second header column
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 14);
    $pdf->SetFillColor(235, 236, 236); // Background color of header 
    $fill = false; // to give alternate background fill color to rows 
/// each record is one row  ///
    foreach ($dbo->query($expense) as $row) {
        $pdf->Cell($width_cell[0], 10, $row['expense_name'], 1, 0, 'C', $fill);
        $pdf->Cell($width_cell[1], 10, $row['amount'], 1, 0, 'L', $fill);

        $pdf->Ln();
        $fill = !$fill; // to give alternate background fill  color to rows
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 16);
    $label = "TOTAL PROPOSED EXPENSES";
    foreach ($dbo->query($totalexpense) as $row) {

        $pdf->Cell($width_cell[0], 10, $label, 0, 0, 'C', $fill);
        $pdf->Cell($width_cell[1], 10, $row['totalExpense'], 0, 0, 'C', $fill);

        global $exp;
        $exp = $row['totalExpense'];
        // to give alternate background fill  color to rows
    }
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFillColor(0, 240, 180);
    $lbl = "BALANCE";
    $balance = $inc - $exp;
    $pdf->Cell($width_cell[0], 10, $lbl, 0, 0, 'C', $fill);
    $pdf->Cell($width_cell[1], 10, $balance, 0, 0, 'C', $fill);
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, '' . $y_row['Fyear'] . ' Budget Comments');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln();
    $pdf->Write(5, '' . $comment . '');
    

/// end of records /// 

    $pdf->Output();
}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $userRow['name']; ?></title>
        <link rel="shortcut icon" href="assets/image/favicon.png" type="image/x-icon" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
        <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
        <link rel="stylesheet" href="assets/css/style2.css" type="text/css"/>
        <link rel="stylesheet" href="assets/css/w3.css" type="text/css"/>
        <link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css"/>
    </head>
    <body>
        <div id="wrap">

            <section  id="top">
                <nav   id="mainnav">
                    <h1 id="sitename" class="logo">
                        <a class="w3-round-xxlarge" href="home.php"> <i class="glyphicon glyphicon-plus-sign"></i> Church B&E Tracker</a>

                    </h1>
                    <ul>

                        <li><a href="profile.php">
                                <span style="font-size: 16px"> <i class="glyphicon glyphicon-user"></i>&nbsp; <?php echo $userRow['name']; ?></span></a></li>
                        <li><a href="logout.php?logout"><span style="font-size: 18px"><i  class="glyphicon glyphicon-log-out">&nbsp;Sign Out</i></span></a></li>

                    </ul>
                </nav>
            </section>
            <section id="page">
                <header id="pageheader" class="w3-round-large homeheader">

                </header>  
                <div class="topnav w3-round-xlarge" id="myTopnav">
                    <a href="home.php"> <i class="glyphicon glyphicon-home"></i> Home</a>
                    <a href="budget.php"> <i class="glyphicon glyphicon-usd"></i> Budget</a>
                    <a href="expenses.php"> <i class="glyphicon glyphicon-apple"></i> Expenses</a>
                    <a href="bills.php"> <i class="glyphicon glyphicon-registration-mark"></i> Bills</a>
                    <a href="income.php"> <i class="glyphicon glyphicon-usd"></i> Income</a>                    

                    <a href="javascript:void(0);" class="icon" onClick="myFunction()">&#9776;</a>

                </div>
                <div style="margin: 0 auto" >
                    <div style="margin: 10px " class="sidebyside"><a href="budget.php" class="btn btn-success w3-round-large " >&laquo; Back  </a></div>

                    <h3  class="page-header">Print <?php echo $userRow['name']; ?> Budget</h3>
                </div> 

                <div  id="register_form_div">
                    <form  method="post" >

                        <div  class="form-group"> 
                            <label for="fyear"> Select Year: </label>
                            <?php
                            $c_id = $_SESSION['user'];
                            $f_query = mysql_query("Select id, year from financial_year WHERE church_id = $c_id order by year DESC");

                            echo "<select title=\" Choose Financial Year\" style=\" data-toggle=\"tooltip\" height: 30px;\" class=\" w3-round-large\" name=\"year\" id=\"fyear\" value='<?php echo $year; ?>'>";

                            while ($row = mysql_fetch_array($f_query)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                            } echo "</select>";
                            ?>       
                        </div> 
                        <span class="text-danger"> <?php echo $yrError; ?></span>
                        <div class="form-group">
                            <label for="comment">Comments:</label>
                            <textarea class="form-control" title="enter comments"  data-toggle="tooltip" rows="5" name="comment" id="comment"></textarea>
                        </div> 

                        <div class="modal-footer">
                            <button title="Click to Print" data-toggle="tooltip" type="submit" name="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-print"></span> &nbsp; Print
                            </button>
                        </div>
                    </form>
                </div>


            </section>
        </div>
        <footer id="pagefooter">
            <div id="f-content">

                <div id="foot_notes">
                    <p style="margin: 0px" align='center'>&copy;<?php echo date("Y"); ?> - Church Budget and Expense Tracker  </p>

                </div>
                <img src="assets/image/bamboo.png" alt="bamboo" id="footerimg" width="96px" height="125px">
            </div>
        </footer>

        <script src="assets/jquery-1.11.3-jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/navigation.js"></script>  
        <script>
                        $(document).ready(function () {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
    </script>



    </body>

</html>
<?php ob_end_flush(); ?>
