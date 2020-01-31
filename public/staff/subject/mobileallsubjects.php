<?php
require_once('../../../private/initialize.php');
$subject_set = find_all_subjects();

 while($subject = mysqli_fetch_assoc($subject_set))
 {
 	$data[] = $subject;
 
 }

 echo json_encode($data);
mysqli_free_result($subject_set);
 db_disconnect($db);
?>