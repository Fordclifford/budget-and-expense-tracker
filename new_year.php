<?php
ob_start();
session_start();
require_once 'config.php';

// if session is not set this will redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
$res = mysql_query("SELECT * FROM church WHERE id=" . $_SESSION['user']);


$userRow = mysql_fetch_array($res);
$error = false;

if (isset($_POST['btn-year'])) {

    // prevent sql injections/ clear user invalid inputs

    $year = trim($_POST['year']);
    $year = strip_tags($year);
    $year = htmlspecialchars($year);
    $church_id = $_SESSION['user'];

    if (empty($year)) {
        $error = true;
        $errTyp = "danger";
        $errMSG = "Error, Check and Try again!";
        $yearError = "Cannot be empty.";
    }  if (strlen($year) != 4 ) {
        $error = true;
        $errTyp = "danger";
        $errMSG = "Error, Check and Try again!";
        $yearError = "Year must have only four digits.";
    }  if ($year <= 0) {
        $error = true;
        $errTyp = "danger";
        $errMSG = "Error, Check and Try again!";
        $yearError = "Year be more than zero.";
    }  if ($year > 2999) {
        $error = true;
        $errTyp = "danger";
        $errMSG = "Error, Check and Try again!";
        $yearError = "Not a valid year.";
    }


    function AddYear() {
        global $year, $error, $errTyp, $church_id,$errMSG;
        if (!$error) {
            $query_insert = mysql_query("INSERT INTO financial_year(year,church_id) VALUES ('$year','$church_id')");
            if ($query_insert) {
                $errTyp = "success";
                $errMSG = "Successfully added..redirecting....";
                header("refresh:5; budget.php");
            } else {
                exit(mysql_error());
                $errMSG = "Unknown Error Occured!, Try again later...";
            }
        }
    }
   
    
    $max_query = "SELECT MAX(year) AS max FROM financial_year WHERE church_id='$church_id'";
    $q_result = mysql_query($max_query);
    $r_count = mysql_fetch_array($q_result);
    $max = $r_count['max'];   
    
    $b_query = "SELECT balance FROM financial_year WHERE year='$max' AND church_id='$church_id'";
    $bq_result = mysql_query($b_query);
    $br_count = mysql_fetch_array($bq_result);
    $bal = $br_count['balance'];

    if ($max < $year) {
        AddYear();
      $id_q =  "SELECT id AS id FROM financial_year WHERE year= '$year' AND church_id='$church_id'";
    $q_result = mysql_query($id_q);
    $r_count = mysql_fetch_array($q_result);
     $id = $r_count['id'];   
     $source ="".$max." Balance Carried Forward";      
     $bal_q =mysql_query("INSERT INTO income_sources(source_name,amount,financial_year,church_id) VALUES ('$source','$bal','$id','$church_id')");
      if (!$bal_q){
         exit(mysql_error());
      }
      $update_query = mysql_query("UPDATE financial_year F
    SET total_income =
    (SELECT SUM(amount) FROM income_sources 
    WHERE church_id = '$church_id' AND financial_year = $id)
    WHERE church_id = '$church_id' AND id = $id");
    if (!$update_query) {
        die("error1!");
        $errMSG = "Sorry Data Could Not Updated !";
        exit(mysql_error($conn));
    }

    $sumincome_query = mysql_query("SELECT total_income AS Income from financial_year  WHERE church_id = $church_id AND id =$id ");
    $sumbills_query = mysql_query("SELECT total_bills AS Bills from financial_year  WHERE church_id = $church_id AND id =$id ");
    $incomerow = mysql_fetch_assoc($sumincome_query);
    $sum_income = $incomerow['Income'];

    $expenserow = mysql_fetch_assoc($sumbills_query);
    $sum_bill = $expenserow['Bills'];
    $balance = $sum_income - $sum_bill;

    $bal_query = mysql_query("UPDATE financial_year 
    SET balance = '$balance' WHERE church_id = $church_id AND id = $id");

    if (!$bal_query) {
        die("error3!");
        exit(mysql_error($conn));
    }
     
     ?>    
      <script>
       alert("New Year \n Balance will be carried forward!")          
        </script>   
        <?php
    }
 if ($max > $year) {
     AddYear();
    }
}
include_once('includes/header.php');
?>
<body>

        <div id="wrapper">

            <!-- Navigation -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user'] == true ) : ?>
                <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="">B&E Tracker</a>
                    </div>
                    <!-- /.navbar-header -->

                    <ul class="nav navbar-top-links navbar-right">
                        <!-- /.dropdown -->

                        <!-- /.dropdown -->
						<li> <a id="notification-icon" name="button" onclick="myFunction()" class="dropbtn"><span id="notification-count"><?php if($count>0) { echo $count; } ?></span><i class="fa fa-envelope fa-fw"></i></a>
			<div id="notification-latest"></div>
			</li>


                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="profile.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                </li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <!-- /.dropdown -->
                    </ul>
                    <!-- /.navbar-top-links -->
                   
                    <div class="navbar-default sidebar" role="navigation">
                        <div class="sidebar-nav navbar-collapse">
                            <ul class="nav" id="side-menu">
                                <li>
                                    <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                                </li>

                                <li <?php echo (CURRENT_PAGE =="balance.php" || CURRENT_PAGE=="balance.php") ? 'class="active"' : '' ; ?>>
                                    <a href="bills.php"><i class="glyphicon glyphicon-registration-mark fa-fw"></i> Bills<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">
                                        <li>
                                            <a href="bills.php"><i class="fa fa-list fa-fw"></i>List all</a>
                                        </li>
                                    <li>
                                        <a href="addbill.php"><i class="fa fa-plus fa-fw"></i>Add New</a>
                                    </li>
                                    </ul>
                                </li>
                                <li>
                                   <a href="expenses.php"> <i class="glyphicon glyphicon-apple"></i> Expenses</a>
                                </li>
                                 <li>
                                   <a href="budget.php"> <i class="glyphicon glyphicon-usd"></i> Budget</a>
                                </li>
                                
                                <li>
                                       <a href="income.php"> <i class="glyphicon glyphicon-usd"></i> Income</a> 

                                </li>
                            </ul>
                        </div>
                        <!-- /.sidebar-collapse -->
                    </div>
                    <!-- /.navbar-static-side -->
                </nav>
            <?php endif; ?>

           <div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">New Financial Year</h1>
        </div>
    </div>
             
                
                <div style="margin: 20px" class ="row">
                    <div class="col-lg-3"><a href="budget.php" class="btn btn-success w3-round-large " >&laquo; Back  </a></div>
                </div>

                <div class="login_form_div w3-round-large" >
                    <form method="post" class="animate" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">


                        <?php
                        if (isset($errMSG)) {
                            ?>
                            <div class="form-group">
                                <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                                    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                                </div>
                            </div>  
<?php } ?>


                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></span>
                                <input style="height:40px" type="number" title="type year" data-toggle="tooltip" name="year" class="form-control w3-round-large" placeholder="Enter Year" value="<?php echo $year; ?>" maxlength="40" />
                            </div>
                            <span class="text-danger"><?php echo $yearError; ?></span>
                        </div>
                        <div >
                            <button type="submit" class="btn btn-block btn-primary" title="click to save" data-toggle="tooltip" name="btn-year"><span class="glyphicon glyphicon-save"> </span> Save</button>
                        </div>                         
                    </form>

                </div>
        </div>

<?php include_once('includes/footer.php'); ?>