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


<body class="hold-transition sidebar-mini layout-fixed">
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
  		
		
<?php  ?>

		<!-- Main Sidebar Container -->
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
					<div class="image">
						<img src="../../app/refer/template/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
							alt="User Image">
					</div>
					<div class="info">
						<a class="d-block"><?php echo "ユーザ名 : " . $_SESSION["loginAccount"] ?></a>
					</div>
				</div>

				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
						data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            <?php if ( count($master_menu) > 0 ) { ?>
              <li class="nav-header">マスター管理</li>  
              <?php foreach ($master_menu as $key => $value) { ?>  
                <li class="nav-item">
                  <a href="<?php echo "../".$value["value"]."/"; ?>" class="nav-link">
                    <i class="nav-icon fas fa-angle-right"></i>
                    <!-- <i class="nav-icon far fa-calendar-alt"></i> -->
                    <p>
                      <?php echo $value["display"]; ?>  
                      <!-- <span class="badge badge-info right">2</span> -->
                    </p>
                  </a>
                </li>
              <?php } ?>  
            <?php } ?>

            <?php if ( count($other_menu) > 0 ) { ?>
              <li class="nav-header">一般管理</li>  
              <?php foreach ($other_menu as $key => $value) { ?>  
                <li class="nav-item">
                  <a href="<?php echo "../".$value["value"]."/"; ?>" class="nav-link">
                    <i class="nav-icon fas fa-angle-right"></i>
                    <p>
                      <?php echo $value["display"] ?>  
                      <!-- <span class="badge badge-info right">2</span> -->
                    </p>
                  </a>
                </li>
              <?php } ?>  
            <?php } ?>
			
			<?php 
			
			?>  
            <?php ?>  
            <?php ?>  
            <?php ?>  
            



						<!-- <li class="nav-item has-treeview menu-open">
							<a href="../../app/topic/" class="nav-link active">
								<i class="nav-icon fas fa-tachometer-alt"></i>
								<p>
									Topic Management
								</p>
							</a>
						</li> -->
          
          

          
          </ul>
		
				</nav>
				<!-- /.sidebar-menu -->

				<div class="logout-panel">
					<form class="logout-btn" action="../../include/common/logout.php"> 
					<button class="btn btn-success" id="logout-button"  href="#" role="button">
						<i class="fas fa-sign-out-alt nav-icon"></i>
						ログアウト
					</button>

         				<!-- <i class="fas fa-sign-out-alt"></i>
						<button type="submit" class="btn btn-success" value="ログアウト" /> -->
					</form>
				</div>


			</div>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark" id="d-page-name">Dashboard</h1>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">

        