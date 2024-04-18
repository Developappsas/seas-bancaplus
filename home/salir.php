<?php include ('../functions.php'); ?>
<?php
    session_destroy();
    header('Location: index.php');
?>
<script>
    window.location.href='index.php';
</script>
