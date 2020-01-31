<?php require_once('../../../private/initialize.php');
require_login();?>

<?php $page_title = 'Specific Page'; ?>
<?php include(SHARED_PATH.'/staff_header.php'); ?>
<?php
 $id = $_GET['id'] ?? '1';
 echo 'My ID: '. h($id);
 $page = find_page_by_id($id);
   $subject = find_subject_by_id($page['subject_id']);
?>
<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/subject/show.php?id='.h(u($subject['id']))); ?>">&laquo; Back to Subject Page</a>

  <div class="page show">

    <h1>Page: <?php echo h($page['menu_name']); ?></h1>
<div class="actions">
  
  <a class="action" target= "_blank" href="<?php echo url_for('index.php?id=' . h(u($page['id'])).'&preview=true'); ?>">PREVIEW</a>
</div>
    <div class="attributes">
     
      <dl>
        <dt>Subject</dt>
        <dd><?php echo h($subject['menu_name']); ?></dd>
      </dl>
      <dl>
      <dl>
        <dt>Menu Name</dt>
        <dd><?php echo h($page['menu_name']); ?></dd>
      </dl>
      <dl>
        <dt>Subject Id</dt>
        <dd><?php echo h($page['subject_id']); ?></dd>
      </dl>
      <dl>
        <dt>Position</dt>
        <dd><?php echo h($page['position']); ?></dd>
      </dl>
      <dl>
        <dt>Visible</dt>
        <dd><?php echo $page['visible'] == '1' ? 'true' : 'false'; ?></dd>
      </dl>
    </div>

  </div>

</div>

<?php include(SHARED_PATH. '/staff_footer.php'); ?>

