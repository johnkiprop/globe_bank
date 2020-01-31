<?php require_once('../private/initialize.php'); ?>

<?php  
$preview =false; 
if (isset($_GET['preview'])) {
$preview = $_GET['preview'] == 'true' && is_logged_in() ? true:false;
}

 if (isset($_GET['id'])) {
	$page_id = $_GET['id'];
	$page = find_page_by_id($page_id,['visible'=> true,'preview' => $preview]);
	if (!$page) {
		redirect_to(url_for('/index.php'));
	}
	$subject_id= $page['subject_id'];
	 $subject = find_subject_by_id($subject_id, ['visible'=> true,'preview' => $preview]); 
  if (!$subject) {
   redirect_to(url_for('/index.php'));
  }
}
	elseif(isset($_GET['subject_id'])){
  $subject_id = $_GET['subject_id'];
  $subject = find_subject_by_id($subject_id, ['visible'=> true,'preview' => $preview]); 
  if (!$subject) {
   redirect_to(url_for('/index.php'));
  }
  $page_set = find_pages_by_subject_id($subject_id,['visible'=> true,'preview' => $preview]);
  $page = mysqli_fetch_assoc($page_set);//acquiring the first page
  mysqli_free_result($page_set);

if (!$page) {
		redirect_to(url_for('/index.php'));
	}
	$page_id = $page['id'];

}
else{
		//nothing selected, show homepage
	} ?>

<?php include(SHARED_PATH . '/public_header.php'); ?>

<div id="main">
<?php include(SHARED_PATH . '/public_navigation.php'); ?>
  <div id="page">
<?php
if (isset($page)) {
	//show the page from the database
	// TODO add html escaping back again
	//next strip tags to ensure only allowed tags
	$allowed_tags = '<div><img><h1><h2><strong><ul><li><p><br><em>';
	echo strip_tags($page['content'], $allowed_tags);
}else{
 include(SHARED_PATH . '/static_homepage.php'); 
}
 ?>
  </div>

</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
