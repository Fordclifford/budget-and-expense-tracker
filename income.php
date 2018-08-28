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
        <title> <?php echo $userRow['name']; ?></title>
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
                    <a href="budget.php"> <i class="glyphicon glyphicon-usd"></i> Budget</a>
                    <a href="expenses.php"> <i class="glyphicon glyphicon-apple"></i> Expenses</a>
                    <a href="bills.php"> <i class="glyphicon glyphicon-registration-mark"></i> Bills</a>


                    <a href="javascript:void(0);" class="icon" onClick="myFunction()">&#9776;</a>

                </div>              
                <div style="margin: 0 auto" >
                    <h3 align='center' class="page-header">Manage <?php echo $userRow['name']; ?> Income</h3>
                </div>

                <div class="row">                    
                    <div class="col-lg-4">
                        <button title="Click to add income"   style="margin-bottom: 20px" class="btn btn-success  w3-round-large "  data-toggle="modal" data-target="#add_new_record_modal"  ><span class="glyphicon glyphicon-plus-sign"></span> Add Income</button>
                    </div>
                    <div class="col-lg-7">
                        <button  data-toggle="collapse"  data-target="#yr_div" title="click to export income records to excel" 
                                 class="btn btn-success  navbar-vav navbar-right w3-round-large"><span class="glyphicon glyphicon-export"></span>  Export Income</button>
                    </div>                    



                    <div style="margin:20px" class=" row animate ">                   
                        <form class="frm">                            

                            <div  class="col-lg-3"> 
                                <label> Year: </label>
                                <?php
                                $c_id = $_SESSION['user'];
                                $f_query = mysql_query("Select id, year from financial_year WHERE church_id = $c_id order by year DESC");

                                echo "<select title=\" Choose Financial Year\" data-toggle=\"tooltip\"  style=\" height: 30px;margin-right:20px\" class=\" w3-round-large\" name=\"year\" id=\"fyear\" value\"echo $fyear\">";
                                echo "<option value=''>Select</option>";
                                while ($row = mysql_fetch_array($f_query)) {
                                    echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
                                } echo "</select>";
                                ?>       
                            </div> 

                            <div class="col-lg-3 ">                                            
                                <button  style="margin-top: 25px" type="button" data-toggle="tooltip"   name="filter" id="filter" title="Click to Search" class="btn btn-info  w3-round-xxlarge"><i class="glyphicon glyphicon-search"></i> Search </button>
                            </div>
                        </form> 
                    </div>
                </div>
                
                <div  id="yr_div" class="collapse">
                   <hr />
                    <div align='center' class="form-group"><h style="font-size: 22px;"><i class="glyphicon glyphicon-export"></i> Export Income to Excel</h>
                    </div>

                    <form style="margin-left: 30%"class="form-inline frm" method="post" action="exportincome.php" >
                        
                        <div class="form-group"> 
                            <label for="fyear"> Select Financial Year: </label>
                            <?php
                            $ch_id = $_SESSION['user'];
                            $fn_query = mysql_query("Select id, year from financial_year WHERE church_id = $ch_id order by year DESC");

                            echo "<select title=\" Choose Financial Year\" style=\" data-toggle=\"tooltip\" height: 30px;\" class=\" w3-round-large\" name=\"year\" id=\"fyear\" value='<?php echo $year; ?>'>";

                            while ($row = mysql_fetch_array($fn_query)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                            } echo "</select>";
                            ?>       
                        </div>                        
                        <div class="form-group">
                            <button title="Click to export" data-toggle="tooltip" type="submit" name="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-export"></span> &nbsp; Export
                            </button>
                        </div>
                    </form>
                </div>


   

                <div class="animate row">
                    <div class="col-md-12">

                        <?php
                        if (isset($errMSG)) {
                            ?>
                            <div class="form-group">
                                <div style="max-width: 80%; margin: 0 auto" class="alert alert-warning">
                                    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                                </div>
                            </div>
                            <?php
                        }                        
                        
                $error = false;
                $sq = "SELECT * FROM income_sources WHERE church_id = '$c_id'";
                $income = mysql_query($sq);
                if (mysql_num_rows($income) == 0) {
                    $error = TRUE;
                    $errTyp = "warning";
                    $errorMSG = "You have not added income for your church, if you have added refresh this page";
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
                        
                   
                        <div class="records_content"></div>
                    </div>
                </div>

                <!-- Modal - Add New Record/User -->
                <div class="modal fade animate" id="add_new_record_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">

                        <div class="modal-content" >
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add Income </h4>
                            </div>
                            <div class="modal-body" style="margin-left: 40px">

                                <div class="form-group">
                                    <label>Select Financial Year: </label>
                                    <div class="input-group">

                                        <span class="input-group-addon"><span class="glyphicon glyphicon-flag "></span></span>

                                        <?php
                                        $chu_id = $_SESSION['user'];
                                        $sqls = "Select id, year from financial_year WHERE church_id = '$chu_id' order by year DESC";
                                        $qs = mysql_query($sqls);
                                        echo "<select title=\" Choose Financial Year\" data-toggle=\"tooltip\"  style=\" height: 40px; width:80%\" class=\"form-control w3-round-large\" name=\"year\" id=\"year\" value\"echo $yr\">";
                                        while ($row = mysql_fetch_array($qs)) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                                        } echo "</select>";
                                        ?>

                                    </div>                              

                                </div>

                                <div class="form-group">
                                    <label for="expense"> Income Source: </label>

                                    <input data-toggle="tooltip"  title="Click anywhere to select existing income source options or enter a value" list="incomes" style="width:80%; height: 40px; background:url('assets/image/image_arrow.PNG')no-repeat right" type="text" name="income" id="source" placeholder="Click for options or type a value " class= "w3-round-large "required />

                                    <datalist id="incomes">
                                        <option value="Sabbath School Collections">Sabbath School Expense Collections</option>
                                        <option value="Church Fund For Needy">Church Fund For Needy</option>
                                        <option value="Combined(church) Budget">Combined Budget Giving</option>
                                        <option value="Welfare Fund">Welfare Fund</option>
                                    </datalist>
                                </div>



                                <div class="form-group">
                                    <label for="expense"> Amount: </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-usd "></span></span>
                                        <input title="Enter Amount" data-toggle="tooltip"  style=" width: 80%; height: 40px" type="number" name="amount" id="amount" placeholder="Amount" class="form-control w3-round-large"/>
                                    </div>

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" data-toggle="tooltip"  title="dismiss modal" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <input type="submit" value="Add Income" title="click to add record" data-toggle="tooltip"  class="btn btn-primary" onclick="addRecord()">
                            </div>
                        </div>                      
                    </div>
                </div>
                <!-- // Modal -->
                <!-- Modal - Update User details -->
                <div class="modal fade animate" id="update_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="width: 80%; margin: 0 auto">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Update</h4>
                            </div>
                            <div class="modal-body" style="margin-left: 40px">
                                <div class="form-group">
                                    <label>Select Financial Year: </label>
                                    <div class="input-group">

                                        <span class="input-group-addon"><span class="glyphicon glyphicon-flag "></span></span>

                                        <?php
                                        $church_id = $_SESSION['user'];
                                        $sql = "Select id, year from financial_year WHERE church_id = '$church_id' order by year DESC";
                                        $q = mysql_query($sql);
                                        echo "<select title=\" Choose Financial Year\" data-toggle=\"tooltip\"  style=\" height: 40px\" class=\"form-control w3-round-large\" name=\"year\" id=\"update_year\" value\"echo $fr\">";
                                        while ($row = mysql_fetch_array($q)) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                                        } echo "</select>";
                                        ?>

                                    </div>                              

                                </div>


                                <div class="form-group">
                                    <label for="source"> Source: </label>

                                    <input title="To view options you must clear data from input field" data-toggle="tooltip"   list="incomes" style="width:80%; height: 50px; background:url('assets/image/image_arrow.PNG')no-repeat right" type="text" name="update_source" id="update_source" placeholder="Click for options or type a value " class= "w3-round-large "required />

                                    <datalist id="incomes">
                                        <option value="Sabbath School Collections">Sabbath School Expense Collections</option>
                                        <option value="Church Fund For Needy">Church Fund For Needy</option>
                                        <option value="Combined(church) Budget">Combined Budget Giving</option>
                                        <option value="Welfare Fund">Welfare Fund</option>
                                    </datalist>
                                </div>


                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-usd "></span></span>
                                        <input title="Edit Amount" data-toggle="tooltip"  style=" width: 80%; height: 50px" type="number" id="update_amount" placeholder="Amount" class="form-control w3-round-large"/>
                                    </div>

                                </div>



                            </div>
                            <div class="modal-footer">
                                <button type="button" title="dismiss " data-toggle="tooltip"  class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="button" title="save" data-toggle="tooltip"  class="btn btn-primary" onclick="UpdateUserDetails()" >Save Changes</button>
                                <input type="hidden" id="hidden_user_id">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal - Add New Record/User -->




                <div style="padding-bottom: 30px"></div>
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
        <script src="assets/js/modal.js"></script>
        <script type="text/javascript" src="assets/js/income_ajax.js"></script>
      
        <script>
                                    $(document).ready(function () {
                                        $('[data-toggle="tooltip"]').tooltip();
                                    });
        </script>
        <!-- Custom JS file -->
        <script type="text/javascript" src="js/script.js"></script>
    </body>

</html>
<?php ob_end_flush(); ?>