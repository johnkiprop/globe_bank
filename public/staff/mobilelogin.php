<?php 

require_once('../../private/initialize.php');

if (is_post_request()) {
  $username = $_POST['username'] ;
  $password = $_POST['password'];
 // $success  = $_POST['success'] ?? '';
  
    $success = "Successful Login";
    $log_in_failure_msg = "Login was unsuccessful";
    $admin = find_admin_by_username($username);
    if ($admin) {
      
       if (password_verify($password, $admin['hashed_password'])) {
         //password matches
        log_in_admin($admin);
        //For simplicity I put the message for the mobile end in errors as well
        $array = array('username' => $username, 'password'=> $password, 'success'=> $success );
        echo json_encode($array);
       }
       else{
        //username found but password doesn't match
           $array = array('username' => $username , 'password'=>"Login Failed" );
        echo json_encode($array);
       }
    }
    else {
      //no username was found
        $array = array('username' => $username , 'password'=>"Login Failed" );
        echo json_encode($array);
    }
}



?>