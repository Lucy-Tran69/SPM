<?php
include "hader.inc";
$menu = $_SESSION['roles'];
?>

<?php if (isset($_SESSION['roles']) === false) {
  exit;
} else {
  $menu = $_SESSION['roles'];
} ?>

<?php 
  $master_menu = array();
  $other_menu  = array();
  $cnt         = strlen("マスター管理");
  //var_dump($menu);

  foreach ($menu as $key => $value) {

    if (strpos($key, "マスター管理") !== false) {
      $display = substr($key, $cnt);
      $display = str_replace("　","", $display);
      $item    = array('display'=>$display, 'value'=>$value);
      array_push($master_menu, $item);
    }
    else {
      $item = array('display'=>$key, 'value'=>$value);
      array_push($other_menu, $item);
    }
  } 

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
	<div class="wrapper">
	<!-- Navbar -->
	<nav class="main-header navbar navbar-expand navbar-white navbar-light ">
		<!-- Left navbar links -->
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
			</li>
			<li class="nav-item d-none d-sm-inline-block">
				<a href="#" class="nav-link">Home</a>
			</li>
			<li class="nav-item d-none d-sm-inline-block">
				<!-- <a href="#" class="nav-link">Contact</a> -->
			</li>
		</ul>

		<!-- Right navbar links -->
		<ul class="navbar-nav ml-auto">

		</ul>
	</nav>
	<!-- /.navbar -->

    <aside class="main-sidebar elevation-4  sidebar-dark-primary">
		<!-- Brand Logo -->
		<a href="/" class="brand-link">
			<img src="../images/head_logo.png" alt="Logo" class="brand-image  elevation-3" style="opacity: .8">
			<span class="brand-text font-weight-light">NCI</span>
		</a>

		<!-- Sidebar -->
		<div class="sidebar">
			<!-- Sidebar user panel (optional) -->
			<div class="user-panel mt-3 pb-3 mb-3 d-flex">
				
				<div class="info">
					<a class="d-block"><?php echo "ユーザ名 : " . $_SESSION["loginAccount"] ?></a>
				</div>
			</div>

			<!-- Sidebar Menu -->
			<nav class="mt-2">
				<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
					<!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
					<?php if ( count($master_menu) > 0 ) { ?>
						<li class="nav-header">マスター管理</li>  
						<?php foreach ($master_menu as $key => $value) { ?>  
							<li class="nav-item">
							<a href="<?php echo "../".$value["value"]."/"; ?>" class="nav-link">
								<i class="nav-icon fas fa-angle-right"></i>
								<p>  <?php echo $value["display"]; ?> </p>
							</a>
							</li>
						<?php } ?>  
					<?php } ?>

					<?php if ( count($other_menu) > 0 ) { ?>
						<li class="nav-header pt-1">一般管理</li>  
						<?php foreach ($other_menu as $key => $value) { ?>  
							<li class="nav-item">
								<a href="<?php echo "../".$value["value"]."/"; ?>" class="nav-link">
									<i class="nav-icon fas fa-angle-right"></i>
									<p> <?php echo $value["display"] ?>  </p>
								</a>
							</li>
						<?php } ?>  
					<?php } ?>


					<li class="nav-header logout-panel pt-1"></li>  
					<li class="nav-item">
						<form id="logout-form" class="logout-btn" action="../../include/common/logout.php"> 
							<a href="javascript:{}" class="nav-link btn btn-danger" onclick="document.getElementById('logout-form').submit();">
								<i class="fas fa-sign-out-alt nav-icon"></i>
								<p>ログアウト</p>
							</a>
						</form>
					</li>
				</ul>
			</nav>
			<!-- /.sidebar-menu -->
			<!-- <nav class="mt-2">
				<form class="logout_button" action="../../include/common/logout.php"> 
				<input type="submit" class="btn btn-success" value="ログアウト" />
				</form>

			</nav> -->

		</div>
		<!-- /.sidebar -->
	</aside>

