<?php
session_start();
require_once 'includes/auth_validate.php';
require_once './config/config.php';
$del_id = filter_input(INPUT_POST, 'id');
if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $usertype=array("admin","super");
  
 if(in_array($_SESSION['user_type'],$usertype)){
    $db = getDbInstance();
    $db->where('id', $del_id);
    $status = $db->delete('conference');
 
    if ($status)
    {
        $_SESSION['info'] = "conference deleted successfully!";
        header('location: conference.php');
        exit;
    }
    if(!$status)
    {
    	$_SESSION['failure'] ="Unable to delete conference, check to confirm that no churches are associated with the conference";
    	header('location: conferences.php');
        exit;
    }
    
}
    else{
         $_SESSION['failure'] = $_SESSION['user_type']."You don't have permission to perform this action";
    	header('location: conferences.php');
        exit;
        }

}
