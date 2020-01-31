<?php
require_once('../../../private/initialize.php');
$page_set = find_all_pages();

 while($page = mysqli_fetch_assoc($page_set))
 {
 	$data[] = $page;
 
 }
 echo json_encode($data);
mysqli_free_result($page_set);
 db_disconnect($db);
?>