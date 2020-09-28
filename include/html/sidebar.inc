<?php
include "hader.inc";
if(session_status()==PHP_SESSION_NONE && isset($_SESSION['roles']) && isset($_SESSION['username']))
{
    session_start();
}
$menu = $_SESSION['roles'];
?>
<style>
  .logout_button{
    position: absolute;
    display: inline-block;
    bottom: 0;
    margin-left: 20%;
  }

</style>
<body class="sidebar-mini layout-fixed layout-navbar-fixed" style="height: auto;">
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #001f3f;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../../app/images/head_logo.png" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">NCI</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a class="d-block"><?php echo "ユーザ名 : " . $_SESSION["loginAccount"] ?></a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php foreach($menu as $key=>$value):?>
                <li class="nav-item" id='<?php echo $value?>' onclick="navigate(this.id)"><a class="nav-link"><?php echo $key?></a></li>
            <?php endforeach; ?>
        </ul>
        <form class="logout_button" action="../../include/logout.php"> 
        <input type="submit" class="btn btn-success" value="ログアウト" />
        <?php include "footer.inc";?> 
        </form>

      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
<script>
function navigate(clicked)
{
    // var str = "../login/"+clicked+".html";
    var str = "../"+clicked;
    window.location.href=str;
}
</script>