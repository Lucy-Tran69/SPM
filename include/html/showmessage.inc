<?php if (isset($_SESSION['err_msg'])) { ?>
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php $msgs = $_SESSION['err_msg'];
    foreach ($msgs as $key => $msg) {
        echo $msg.'<br>';
    }
    ?>
</div>
<?php } ?>

<?php if (isset($_SESSION['warn_msg'])) { ?>
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php $msgs = $_SESSION['warn_msg'];
    foreach ($msgs as $key => $msg) {
        echo $msg.'<br>';
    }
    ?>
</div>
<?php } ?>
<?php if (isset($_SESSION['success_msg'])) { ?>
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php $msgs = $_SESSION['success_msg'];
    foreach ($msgs as $key => $msg) {
        echo $msg.'<br>';
    }
    ?>
</div>
<?php }

if (isset($_SESSION['err_msg'])) { 
    unset($_SESSION['err_msg']);
}
if (isset($_SESSION['warn_msg'])) { 
    unset($_SESSION['warn_msg']);
}
if (isset($_SESSION['success_msg'])) { 
    unset($_SESSION['success_msg']);
}
?>

