<?php
function validate_page($page) {

  $errors = [];
  
  // menu_name
  
  if(is_blank($page['subject_id'])) {
    $errors[] = "Subject cannot be blank.";
  }
  if(is_blank($page['menu_name'])) {
    $errors[] = "Name cannot be blank.";
  }
else if(!has_length($page['menu_name'], ['min' => 2, 'max' => 255])) {
    $errors[] = "Name must be between 2 and 255 characters.";
  }
if(is_blank($page['content'])) {
    $errors[] = "Content cannot be blank.";
  }
else if(!has_length($page['content'], ['min' => 2])) {
    $errors[] = "Content must be more than 2 characters.";
  }
  // position
  // Make sure we are working with an integer
  $current_id = $page['id']?? '0';
  if (!has_unique_page_menu_name($page['menu_name'], $current_id)) {
    $errors[] = "Menu name must be unique";
  }
  $postion_int = (int) $page['position'];
  if($postion_int <= 0) {
    $errors[] = "Position must be greater than zero.";
  }
  if($postion_int > 999) {
    $errors[] = "Position must be less than 999.";
  }

  // visible
  // Make sure we are working with a string
  $visible_str = (string) $page['visible'];
  if(!has_inclusion_of($visible_str, ["0","1"])) {
    $errors[] = "Visible must be true or false.";
  }

  return $errors;
}
function validate_subject($subject) {

  $errors = [];
  
  // menu_name
  if(is_blank($subject['menu_name'])) {
    $errors[] = "Name cannot be blank.";
  }
else if(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
    $errors[] = "Name must be between 2 and 255 characters.";
  }

  // position
  // Make sure we are working with an integer
  $postion_int = (int) $subject['position'];
  if($postion_int <= 0) {
    $errors[] = "Position must be greater than zero.";
  }
  if($postion_int > 999) {
    $errors[] = "Position must be less than 999.";
  }

  // visible
  // Make sure we are working with a string
  $visible_str = (string) $subject['visible'];
  if(!has_inclusion_of($visible_str, ["0","1"])) {
    $errors[] = "Visible must be true or false.";
  }

  return $errors;
}
function find_all_subjects($options = []){
global $db;
$visible = $options['visible'] ?? false; 
$preview= $options['preview'] ?? false;
$sql = "SELECT * FROM subjects ";
if ($visible && !$preview) {
  $sql .= "WHERE visible = true ";
}
$sql .= "ORDER BY position ASC";
$subject_set = mysqli_query($db, $sql); 
confirm_result_set($subject_set);
return $subject_set;
}


function delete_subject($id){
	global $db;

  $old_subject = find_subject_by_id($id);
  $old_position = $old_subject['position'];
  shift_subject_positions($old_position, 0, $id);

	$sql = "DELETE FROM subjects ";
   $sql .= "WHERE id='". db_escape($db,$id) ."' ";
   $sql .= "LIMIT 1";

   $result = mysqli_query($db, $sql);
   //DELETE statements return $result true/false
   if ($result) {
    return true;
   }
   else{
    //DELETE failed
echo mysqli_error($db);
db_disconnect($db);
exit;
   }
}
function insert_subjects($subject){
global $db;
$errors = validate_subject($subject);
if (!empty($errors)) {
  return $errors;
}
shift_subject_positions(0, $subject['position']);
$sql =  "INSERT INTO subjects ";
$sql .= "(menu_name, position, visible) ";
$sql .= "VALUES(";
$sql .= "'" . db_escape($db,$subject['menu_name']) . "',";
$sql .= "'" .  db_escape($db,$subject['position']) . "',";
$sql .= "'" .  db_escape($db,$subject['visible']) . "'";
$sql .= ")";
$result = mysqli_query($db, $sql);
//for INSERT statements result is true or false
if ($result) {
	// INSERT succeeds
    /* $new_id = mysqli_insert_id($db);
     redirect_to(url_for('staff/subject/show.php?id='. $new_id)); */
     return true;
}
else{
	//INSERT failed
echo mysqli_error($db);
db_disconnect($db);
exit;	
}
}
function insert_page($page){
global $db;
$errors = validate_page($page);
if (!empty($errors)) {
  return $errors;
}
 shift_page_positions(0, $page['position'], $page['subject_id']);

$sql =  "INSERT INTO pages ";
$sql .= "(subject_id, menu_name, position, visible, content) ";
$sql .= "VALUES(";
$sql .= "'" .  db_escape($db,$page['subject_id']) . "',";
$sql .= "'" .  db_escape($db,$page['menu_name']). "',";
$sql .= "'" .  db_escape($db,$page['position']) . "',";
$sql .= "'" .  db_escape($db,$page['visible']). "' ,";
$sql .= "'" .  db_escape($db,$page['content']) . "'";
$sql .= ")";
$result = mysqli_query($db, $sql);
//for INSERT statements result is true or false
if ($result) {
	// INSERT succeeds
    /* $new_id = mysqli_insert_id($db);
     redirect_to(url_for('staff/subject/show.php?id='. $new_id)); */
     return true;
}
else{
	//INSERT failed
echo mysqli_error($db);
db_disconnect($db);
exit;	
}
}
function update_subject($subject){
global $db;

$errors = validate_subject($subject);
if (!empty($errors)) {
  return $errors;
}
$old_subject = find_subject_by_id($subject['id']);
$old_position = $old_subject['position'];
shift_subject_positions($old_position, $subject['position'],$subject['id'] );

$sql = "UPDATE subjects SET ";
$sql .= "menu_name='". db_escape($db,$subject['menu_name']). "', ";
$sql .= "position='". db_escape($db,$subject['position']). "', " ;
$sql .= "visible='". db_escape($db,$subject['visible']). "' " ;
$sql .= "WHERE id='" . db_escape($db, $subject['id']) . "' ";
$sql .= "LIMIT 1";

$result = mysqli_query($db, $sql);
//remember for UPDATE statements the result is either true or false
if($result){
  return true;
}
else{
  //UPDATE failed
  echo mysqli_error($db);
db_disconnect($db);
exit; 
}
}
function update_page($page) {
    global $db;
$errors = validate_page($page);
if (!empty($errors)) {
  return $errors;
}
   
  $old_page = find_page_by_id($page['id']);
  $old_position = $old_page['position'];
  shift_page_positions($old_position, $page['position'],$page['subject_id'],$page['id']);

    $sql = "UPDATE pages SET "; 
    $sql .= "subject_id='" . db_escape($db, $page['subject_id']). "', ";
    $sql .= "menu_name='" .  db_escape($db,$page['menu_name']). "', ";
    $sql .= "position='" .  db_escape($db,$page['position']) . "', ";
    $sql .= "visible='" .  db_escape($db,$page['visible']) . "', ";
    $sql .= "content='" .  db_escape($db,$page['content']) . "' ";
    $sql .= "WHERE id='" .  db_escape($db,$page['id']) . "' ";
    $sql .= "LIMIT 1";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }

  }
function delete_page($id) {
    global $db;
$old_page = find_page_by_id($id);
$old_position = $old_page['position'];
shift_page_positions($old_position, 0,$old_page['subject_id'],$id);
    $sql = "DELETE FROM pages ";
    $sql .= "WHERE id='" .  db_escape($db,$id) . "' ";
    $sql .= "LIMIT 1";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
function find_all_pages(){
global $db;
$sql = "SELECT * FROM pages ";
$sql .= "ORDER BY subject_id ASC, position ASC";
$pages = mysqli_query($db, $sql); 
confirm_result_set($pages);
return $pages;
}
function find_subject_by_id($id,$options=[]){
global $db;
$visible= $options['visible'] ?? false;
$preview= $options['preview'] ?? false;
 $sql = "SELECT * FROM subjects ";
 $sql .= "WHERE id='".  db_escape($db,$id). "' ";
 if ($visible && !$preview) {
$sql .= "AND visible = true";
 }
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
 $subject = mysqli_fetch_assoc($result);
 mysqli_free_result($result);
 return $subject; //returns as assoc. array 
}
function find_page_by_id($id, $options=[]){
global $db;
$visible= $options['visible'] ?? false;
$preview= $options['preview'] ?? false;
 $sql = "SELECT * FROM pages ";
 $sql .= "WHERE id='".  db_escape($db,$id). "' ";
 if ($visible && !$preview) {
$sql .= "AND visible = true";
 }
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
 $page = mysqli_fetch_assoc($result);
 mysqli_free_result($result);
 return $page; //returns as assoc. array 
}
function find_pages_by_subject_id($subject_id, $options =[]){
global $db;
$visible = $options['visible'] ?? false; 
$preview= $options['preview'] ?? false;
 $sql = "SELECT * FROM pages ";
 $sql .= "WHERE subject_id='".  db_escape($db,$subject_id). "' ";
 if ($visible && !$preview) {
   $sql .= "AND visible = true ";
 }
 $sql .= "ORDER BY position ASC";
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
 return $result;
}
function count_pages_by_subject_id($subject_id, $options =[]){
global $db;
$visible = $options['visible'] ?? false; 
$preview= $options['preview'] ?? false;
 $sql = "SELECT COUNT(id) FROM pages ";
 $sql .= "WHERE subject_id='".  db_escape($db,$subject_id). "' ";
 if ($visible && !$preview) {
   $sql .= "AND visible = true ";
 }
 $sql .= "ORDER BY position ASC";
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
$row = mysqli_fetch_row($result);
mysqli_free_result($result);
$count = $row[0];
 return $count;
}



function find_all_admins(){
global $db;
$sql = "SELECT * FROM admins ";
$sql .= "ORDER BY last_name ASC, first_name ASC";
$admin_set = mysqli_query($db, $sql); 
confirm_result_set($admin_set);
return $admin_set;
}

function insert_admins($admin){
global $db;
$errors = validate_admin($admin);
if (!empty($errors)) {
  return $errors;
}
 
 $hashed_password = password_hash($admin['password'], PASSWORD_BCRYPT);

$sql =  "INSERT INTO admins ";
$sql .= "(first_name, last_name, email, username, hashed_password) ";
$sql .= "VALUES(";
$sql .= "'" . db_escape($db,$admin['first_name']) . "',";
$sql .= "'" .  db_escape($db,$admin['last_name']) . "',";
$sql .= "'" .  db_escape($db,$admin['email']) . "',";
$sql .= "'" .  db_escape($db,$admin['username']) . "',";
$sql .= "'" .  db_escape($db,$hashed_password) . "'";
$sql .= ")";
$result = mysqli_query($db, $sql);
//for INSERT statements result is true or false
if ($result) {
  // INSERT succeeds
    /* $new_id = mysqli_insert_id($db);
     redirect_to(url_for('staff/subject/show.php?id='. $new_id)); */
     return true;
}
else{
  //INSERT failed
echo mysqli_error($db);
db_disconnect($db);
exit; 
}
}
function find_admin_by_id($id){
global $db;
 $sql = "SELECT * FROM admins ";
 $sql .= "WHERE id='".  db_escape($db,$id). "' ";
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
 $admin = mysqli_fetch_assoc($result);
 mysqli_free_result($result);
 return $admin; //returns as assoc. array 
}
function find_admin_by_username($username){
global $db;
 $sql = "SELECT * FROM admins ";
 $sql .= "WHERE username='".  db_escape($db,$username). "' ";
 $result = mysqli_query($db, $sql);
 confirm_result_set($result);
 $admin = mysqli_fetch_assoc($result);
 mysqli_free_result($result);
 return $admin; //returns as assoc. array 
}

function delete_admin($id) {
    global $db;

    $sql = "DELETE FROM admins ";
    $sql .= "WHERE id='" .  db_escape($db,$id) . "' ";
    $sql .= "LIMIT 1";
    $result = mysqli_query($db, $sql);

    // For DELETE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // DELETE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
  function update_admin($admin){
global $db;

$password_sent = !is_blank($admin['password']);

$errors = validate_admin($admin, ['password_required'=> $password_sent]);
if (!empty($errors)) {
  return $errors;
}


 $hashed_password = password_hash($admin['password'], PASSWORD_BCRYPT);

$sql = "UPDATE admins SET ";
$sql .= "first_name='". db_escape($db,$admin['first_name']). "', ";
$sql .= "last_name='". db_escape($db,$admin['last_name']). "', ";
$sql .= "email='". db_escape($db,$admin['email']). "', " ;
if ($password_sent) {
$sql .= "hashed_password='". db_escape($db,$hashed_password). "', " ;
}
$sql .= "username='". db_escape($db,$admin['username']). "' " ;
$sql .= "WHERE id='" . db_escape($db, $admin['id']) . "' ";
$sql .= "LIMIT 1";

$result = mysqli_query($db, $sql);
//remember for UPDATE statements the result is either true or false
if($result){
  return true;
}
else{
  //UPDATE failed
  echo mysqli_error($db);
db_disconnect($db);
exit; 
}
}
function validate_admin($admin, $options = []) {

  $errors = [];
  $password_required = $options['password_required'] ?? true;
  // first_name
  if(is_blank($admin['first_name'])) {
    $errors[] = "First Name cannot be blank.";
  }
elseif(!has_length($admin['first_name'], ['min' => 2, 'max' => 255])) {
    $errors[] = "First Name must be between 2 and 255 characters.";
  }
 // last_name
  if(is_blank($admin['last_name'])) {
    $errors[] = "Last Name cannot be blank.";
  }
elseif(!has_length($admin['last_name'], ['min' => 2, 'max' => 255])) {
    $errors[] = "Last Name must be between 2 and 255 characters.";
  }
 //email check
  if(is_blank($admin['email'])) {
    $errors[] = "Email cannot be blank.";
  }
elseif(!has_length($admin['email'], ['min' => 0,'max' => 255])) {
    $errors[] = "Email maximum is 255 characters.";
  }
 if (!has_valid_email_format($admin['email'])) {
    $errors[] = "Enter a valid email.";
 }
  //username
  if(is_blank($admin['username'])) {
    $errors[] = "Username cannot be blank.";
  }
elseif(!has_length($admin['username'], ['min' => 8, 'max' => 255])) {
    $errors[] = "Username must be between 8 and 255 characters.";
  }
   $current_id = $admin['id']?? '0';
  if (!has_unique_admin_username($admin['username'], $current_id)) {
    $errors[] = "Username must be unique";
  }
  if ($password_required) {
   if(is_blank($admin['password'])) {
      $errors[] = "Password cannot be blank.";
    } elseif (!has_length($admin['password'], array('min' => 12))) {
      $errors[] = "Password must contain 12 or more characters";
    } elseif (!preg_match('/[A-Z]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 uppercase letter";
    } elseif (!preg_match('/[a-z]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 lowercase letter";
    } elseif (!preg_match('/[0-9]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 number";
    } elseif (!preg_match('/[^A-Za-z0-9\s]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 symbol";
    }

    if(is_blank($admin['confirm_password'])) {
      $errors[] = "Confirm password cannot be blank.";
    } elseif ($admin['password'] !== $admin['confirm_password']) {
      $errors[] = "Password and confirm password must match.";
    }
  }

   
  return $errors;
}
function shift_subject_positions($start_pos, $end_pos, $current_id=0) {
    global $db;

    if($start_pos == $end_pos) { return; }

    $sql = "UPDATE subjects ";
    if($start_pos == 0) {
      // new item, +1 to items greater than $end_pos
      $sql .= "SET position = position + 1 ";
      $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
    } elseif($end_pos == 0) {
      // delete item, -1 from items greater than $start_pos
      $sql .= "SET position = position - 1 ";
      $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
    } elseif($start_pos < $end_pos) {
      // move later, -1 from items between (including $end_pos)
      $sql .= "SET position = position - 1 ";
      $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
      $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
    } elseif($start_pos > $end_pos) {
      // move earlier, +1 to items between (including $end_pos)
      $sql .= "SET position = position + 1 ";
      $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position < '" . db_escape($db, $start_pos) . "' ";
    }
    // Exclude the current_id in the SQL WHERE clause
    $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }

function shift_page_positions($start_pos, $end_pos, $subject_id, $current_id=0) {
    global $db;

    if($start_pos == $end_pos) { return; }

    $sql = "UPDATE pages ";
    if($start_pos == 0) {
      // new item, +1 to items greater than $end_pos
      $sql .= "SET position = position + 1 ";
      $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
    } elseif($end_pos == 0) {
      // delete item, -1 from items greater than $start_pos
      $sql .= "SET position = position - 1 ";
      $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
    } elseif($start_pos < $end_pos) {
      // move later, -1 from items between (including $end_pos)
      $sql .= "SET position = position - 1 ";
      $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
      $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
    } elseif($start_pos > $end_pos) {
      // move earlier, +1 to items between (including $end_pos)
      $sql .= "SET position = position + 1 ";
      $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
      $sql .= "AND position < '" . db_escape($db, $start_pos) . "' ";
    }
    // Exclude the current_id in the SQL WHERE clause
    $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";
    $sql .= "AND subject_id = '" . db_escape($db, $subject_id) . "'";

    $result = mysqli_query($db, $sql);
    // For UPDATE statements, $result is true/false
    if($result) {
      return true;
    } else {
      // UPDATE failed
      echo mysqli_error($db);
      db_disconnect($db);
      exit;
    }
  }
?>