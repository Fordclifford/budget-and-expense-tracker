<?php
ob_start();
session_start();
require_once 'config.php';
require_once 'db.php';

// if session is not set this will redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
$res = mysql_query("SELECT * FROM church WHERE id=" . $_SESSION['user']);


$userRow = mysql_fetch_array($res);
$church_id = $_SESSION['user'];

if (isset($_GET['delete_id'])) {
    // select image from db to delete
  
    $stmt_select = $DB_con->prepare('SELECT image FROM bill WHERE id =:uid');
    $stmt_select->execute(array(':uid' => $_GET['delete_id']));
    $imgRow = $stmt_select->fetch(PDO::FETCH_ASSOC);
    $img ="no-image.png";
      if ($imgRow['image']!== $img){
    unlink("uploads/" . $imgRow['image']);
    }
    // it will delete an actual record from db
    $stmt_delete = $DB_con->prepare('DELETE FROM bill WHERE id =:uid');
    $stmt_delete->bindParam(':uid', $_GET['delete_id']);


    $id = $_GET['delete_id'];
    $id_query = mysql_query("SELECT financial_year,source from bill WHERE id =$id");
    if (!$id_query) {
        die("error2!");
        exit(mysql_error($conn));
    }
    $id_row = mysql_fetch_assoc($id_query);
    $yr_id = $id_row['financial_year'];
    $category = $id_row['source'];

    $stmt_delete->execute();
    $sum_amt = mysql_query("SELECT SUM(amount) AS sum FROM bill WHERE source=$category AND church_id=$church_id AND financial_year=$yr_id");
    $sum_row = mysql_fetch_assoc($sum_amt);
    $sum = $sum_row['sum'];

    $bala = mysql_query("SELECT amount AS amount FROM budget_expenses WHERE sid=$category ");
    $bal_row = mysql_fetch_assoc($bala);
    $amt_val = $bal_row['amount'];

    $bal_val = $amt_val - $sum;
    $upd_query = mysql_query("UPDATE budget_expenses SET balance='$bal_val' WHERE sid=$category");

    if (!$upd_query) {
        die("error4!");
        exit(mysql_error($conn));
    }
 $ch =mysql_query("Select *  FROM bill WHERE church_id = $church_id AND financial_year = $yr_id ");
     if (mysql_num_rows($ch) > 0) {
    $update_query = mysql_query("UPDATE financial_year F
    SET total_bills =
    (SELECT SUM(amount) FROM bill 
    WHERE church_id = '$church_id' AND financial_year = $yr_id)
    WHERE church_id = '$church_id' AND id = $yr_id");
    if (!$update_query) {
        die("error1!");
        $errMSG = "Sorry Data Could Not Updated !";
        exit(mysql_error($conn));
    }
        }
         if (mysql_num_rows($ch) == 0) {
             $tot =0.00;
              $update_query = mysql_query("UPDATE financial_year F
    SET total_bills = '$tot' WHERE church_id = '$church_id' AND id = $yr_id");
    if (!$update_query) {
        die("error1!");
        $errMSG = "Sorry Data Could Not Updated !";
        exit(mysql_error($conn));
    }
         }
        
    $sumincome_query = mysql_query("SELECT total_income AS Income from financial_year  WHERE church_id = $church_id AND id =$yr_id ");
    $sumbills_query = mysql_query("SELECT total_bills AS Bills from financial_year  WHERE church_id = $church_id AND id =$yr_id ");
    $incomerow = mysql_fetch_assoc($sumincome_query);
    $sum_income = $incomerow['Income'];


    $expenserow = mysql_fetch_assoc($sumbills_query);
    $sum_bill = $expenserow['Bills'];

    $balance = $sum_income - $sum_bill;

    if ($balance == 0) {
        $balan = 0.00;
    } if ($balance !=0) {
        $balan = $balance;
    }

    $bal_query = mysql_query("UPDATE financial_year 
    SET balance = '$balan' WHERE church_id = $church_id AND id = $yr_id");

    if (!$bal_query) {
        die("error3!");
        exit(mysql_error($conn));
    }

    header("Location: bills.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Welcome - <?php echo $userRow['name']; ?></title>
        <link rel="shortcut icon" href="assets/image/favicon.png" type="image/x-icon" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
        <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
        <link rel="stylesheet" href="assets/css/style2.css" type="text/css"/>
        <link rel="stylesheet" href="assets/css/w3.css" type="text/css"/>
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css">
       <link rel="stylesheet" href="assets/css/font-awesome.min.css" type="text/css"/>
    </head>
    <body>
        <div id="wrap">

        <section  id="top">                
                <nav    class="navbar  navbar-inverse w3-round-xlarge">
                    <div class="container-fluid">
                        <div class="navbar-header " >
                            <a  class="w3-round-xxlarge navbar-brand" title="B&E Tracker Home" href="home.php"><img src="assets/image/log.png" style="height:48px; width:180px;" class="img-responsive w3-round-xxlarge" ></a>


                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar"><span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>                        
                            </button>
                        </div>
                            <ul class="nav navbar-nav navbar-right ">
                                <ul class="nav navbar-top-links navbar-right">
                            <li class="dropdown">
                                <a id="logged_in_user" class="dropdown-toggle logged-in-user" data-toggle="dropdown" href="profile.php">
                                    <i class="fa fa-user fa-fw"></i> <?php echo $_SESSION['name']; ?> <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="profile.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                                    </li>
                                   
                                    <li class="divider"></li>
                                    <li><a href="index.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                    </li>
                                </ul>
                                <!-- /.drop down-user -->
                            </li>
                        </ul>
                    </ul>
                      
                    </div>
                </nav>

            </section>
           
            <section id="page">
                <header id="pageheader" class="w3-round-xlarge homeheader">

                </header>  
                <div class="topnav w3-round-xlarge" id="myTopnav">
                    <a href="home.php"> <i class="glyphicon glyphicon-home"></i> Home</a>
                    <a href="budget.php"> <i class="glyphicon glyphicon-briefcase"></i> Budget</a>
                    <a href="expenses.php"> <i class="glyphicon glyphicon-apple"></i> Expenses</a>

                    <a href="income.php"> <i class="glyphicon glyphicon-usd"></i> Income</a>

                    <a href="javascript:void(0);" class="icon" onClick="myFunction()">&#9776;</a>

                </div>


                <div style="margin: 0 auto" >
                    <h3 align='center' class="page-header">Manage <?php echo $userRow['name']; ?> Bills</h3>
                </div>



                <div style="margin: 20px"class=" search animate ">                   
                    <form class="frm form-inline">

                        <div  class="form-group "> 
                            <label for="year"> &nbsp;&nbsp;Year: </label>
                            <?php
                            $church_ids = $_SESSION['user'];
                            $sqls = "Select id, year from financial_year WHERE church_id = '$church_ids' order by year DESC";
                            $qs = mysql_query($sqls);

                            echo "<select title=\" Choose Financial Year\" data-toggle=\"tooltip\"  style=\" height: 30px;max-width:100%\" class=\" w3-round-large\" name=\"year\" id=\"year\" value\"echo $fyear\">";
                            echo "<option value=''>-Select-</option>";
                            while ($row = mysql_fetch_array($qs)) {
                                echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
                            } echo "</select>";
                            ?>       
                        </div> 

                        <div class="form-group ">                                            
                            <button  type="button"  name="filter" id="filter" title="Click to Search" data-toggle="collapse"  data-target="#month" class="btn btn-info  w3-round-xxlarge"><i class="glyphicon glyphicon-search"></i> Search </button>
                        </div>
                    </form> 
                </div>
                <div class="row">               

                    <div class="col-lg-4 "><a title="Click to Add Bill" data-toggle="tooltip"  onclick="return confirm('Are You Sure to Add Bill?')" href="addbill.php" class="btn btn-success w3-round-large glyphicon glyphicon-plus-sign " > Add Bill</a></div>

                    <div class="col-lg-4"><a title="Click to Print to Pdf" data-toggle="tooltip"  onclick="return confirm('Are You Sure to Print Bills?')" href="dateBillsPdf.php"class="btn btn-success w3-round-large glyphicon glyphicon-print"  > Print Bills</a></div>

                    <div class="col-lg-3"><a title="Click to Export to Excel" data-toggle="tooltip"  onclick="return confirm('Are You Sure to export?')" href="dateBillsExcel.php" class="btn btn-success w3-round-large glyphicon glyphicon-export " > Export Bill</a></div>

                </div>

                <hr />


                <div class="row">
                    <div class="col-md-12">
                        <?php
                         $error = false;
                $sq = "SELECT * FROM bill WHERE church_id = '$church_id'";
                $bill = mysql_query($sq);
                if (mysql_num_rows($bill) == 0) {
                    $error = TRUE;
                    $errTyp = "warning";
                    $errorMSG = "You have not added bills for your church, if you have added refresh this page";
                }
// Design initial table header 
                if (isset($errorMSG)) {
                    ?>
                    <div style="background-color: #ff9900" class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display = 'none';">&times;</span>
                        <?php echo $errorMSG; ?>
                    </div> 
                    <?php
                }
                ?>

                        <div class="animate records_content"></div>
                    </div>
                </div>
                <!-- Modal - Add New Record/User -->

                <div class="modal fade animate" id="add_new_record_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">

                        <div class="modal-content" >
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Bill </h4>
                            </div>

                            <form style="margin: 0 auto; width: 100%" name="bill" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="DateCheck()" enctype="multipart/form-data">
                                <?php
                                if (isset($errMSG)) {
                                    ?>
                                    <div class="form-group">
                                        <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                                            <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                                        </div>
                                    </div>  
                                <?php } ?>

                                <hr />
                                <div class="form-group">
                                    <label>Category: </label>
                                    <?php
                                    $sql = "Select id, expense_name from budget_expenses church_id = '$church_id'";
                                    $q = mysql_query($sql);
                                    echo "<select style=\" width: 60%; margin: 20px;height: 40px\" class=\"w3-round-large\" name=\"category\" id=\"category\">";
                                    echo "<option value=\"Select Category\" size =30 ></option>";

                                    while ($row = mysql_fetch_array($q)) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['expense_name'] . "</option>";
                                    } echo "</select>";
                                    ?>


                                </div>
                                <div class="form-group">

                                    <div class="input-group">
                                        <span class="input-group-addon"><a href="javascript:NewCal('date','ddmmyyyy')"><i class="glyphicon glyphicon-calendar glyphicon-lg"></i></a></span>
                                        <input style="height:40px; width: 80%;  margin-top:0px" type="text" id="date" name="date" class="form-control w3-round-large" placeholder="Select Date" readonly="true" value="<?php echo $date; ?>"required/>
                                        <span class="text-danger"><?php echo $dateError; ?></span></div>
                                </div>



                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-dollar "></span></span>
                                        <input style=" width: 80%; height: 40px;" type="number" name="amount" id="amount" placeholder="Enter Amount" class="form-control w3-round-large" value="<?php echo $amount; ?>"/>
                                        <span class="text-danger"><?php echo $amtError; ?></span> </div>
                                </div>

                                <div class="modal-upld"> 

                                    <div class="sidebyside">
                                        <input type="file" name="userfile" id="userfile" accept="image/*" /><span class="text-danger"><?php echo $imgError; ?></span>
                                    </div>
                                </div>


                                <div style="margin-top:50px" class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-file "></span></span>
                                        <input style=" width: 80%; height: 40px; margin-top: 0px" type="text" name="desc" id="desc" placeholder="Description " class="form-control w3-round-large" value="<?php echo $desc; ?>"/>
                                        <span class="text-danger"><?php echo $descError; ?></span></div>
                                </div>

                                <div class="modal-footer">
                                    <button type="reset" class="btn btn-default" >Cancel</button>
                                    <input type="submit" name="submit" value="Save" class="btn btn-primary" onclick="addRecord()"/><span class="glyphicon glyphicon-save"></span>
                                </div>
                            </form>



                        </div>
                    </div>
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
        <script src="assets/js/dateTimePicker.js"></script>
        <script src="assets/js/modalBills.js"></script>

        <script type="text/javascript" src="assets/js/ajax.js"></script>
        <script>
                                        $(document).ready(function () {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
        </script>
    </body>

</html>
<?php ob_end_flush(); ?>