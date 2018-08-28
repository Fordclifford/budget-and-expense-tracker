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
       <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="assets/css/w3.css" type="text/css"/>
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
                    <a href="expenses.php"> <i class="glyphicon glyphicon-apple"></i> Expenses</a>
                    <a href="bills.php"> <i class="glyphicon glyphicon-registration-mark"></i> Bills</a>
                    <a href="income.php"> <i class="glyphicon glyphicon-usd"></i> Income</a>

                    <a href="javascript:void(0);" class="icon" onClick="myFunction()">&#9776;</a>

                </div>                               

                <div style="margin: 0 auto" >
                    <h3 align='center' class="page-header">View <?php echo $userRow['name']; ?> Budget</h3>
                </div>     

                <div class="col-lg-4">
                    <div  class=" animate "><a onclick="return confirm('Sure to Add?')" style="margin: 20px" data-toggle="tooltip" title="click to add new financial year" href="new_year.php" class="btn btn-success w3-round-large"  ><span class="glyphicon glyphicon-plus-sign"></span> New Year</a></div>

                    <div  class=" animate "><a onclick="return confirm('Sure to Print?')" style="margin-left: 20px" data-toggle="tooltip" title="click to print budget to pdf" href="printBudgetPdf.php" class="btn btn-success w3-round-large"  ><span class="glyphicon glyphicon-print"></span> Print Budget</a></div>
                </div>
                <div class=" search">               
                    <form class="frm">                       

                        <div  class="sidebyside"> 
                            <label> Year: </label>
                            <?php
                            $c_id = $_SESSION['user'];
                            $f_query = mysql_query("Select id, year from financial_year WHERE church_id = $c_id order by year DESC");

                            echo "<select title=\" Choose Financial Year\" data-toggle=\"tooltip\" style=\" height: 30px;\" class=\" w3-round-large\" name=\"year\" id=\"fyear\" value\"echo $fyear\">";
                            echo "<option value=''>Select</option>";
                            while ($row = mysql_fetch_array($f_query)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                            } echo "</select>";
                            ?>       
                        </div>  

                        <div style="padding-top: 20px;"class="sidebyside ">                                            
                            <button  type="button" style="height: 40px;"  name="filter" id="filter" data-toggle="tooltip" title="Click to Search" class="btn btn-info  glyphicon glyphicon-eye-open w3-round-xxlarge"> View </button>
                        </div>
                    </form> 
                </div>


                <hr />

                <div class=" animate row">
                    <div class="col-md-12">


                        <div class="record_content"></div>

                        <div class="records_content"></div>
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
    </body>
    <script src="assets/jquery-1.11.3-jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/navigation.js"></script>           
    <script src="assets/js/modal3.js"></script>
    <script src="assets/js/budget_expenses_ajax.js"></script>
    <script src="assets/js/budget_income_ajax.js"></script>
    <script>
                        $(document).ready(function () {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
    </script>

    <!-- Custom JS file -->
    <script type="text/javascript" src="js/script.js"></script>
</html>
<?php ob_end_flush(); ?>