<?php ob_start();
if(session_status()==PHP_SESSION_NONE && isset($_SESSION['roles']) && isset($_SESSION['username']))
{
    session_start();
}

?>

<body class="hold-transition sidebar-mini layout-fixed">
	<div class="wrapper">

		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="index3.html" class="nav-link">Home</a>
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



		<!-- Main Sidebar Container -->
		<aside class="main-sidebar elevation-4 sidebar-light-primary">
			<!-- Brand Logo -->
			<a href="index3.html" class="brand-link">
				<img src="../images/head_logo.png" alt="Logo" class="brand-image  elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light">NCI</span>
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="../../include/template/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
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
						<li class="nav-item has-treeview menu-open">
							<a href="../../app/topic/topics.html" class="nav-link active">
								<i class="nav-icon fas fa-tachometer-alt"></i>
								<p>
									Topic Management
								</p>
							</a>
						</li>
					</ul>
				</nav>
				<!-- /.sidebar-menu -->
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
							<h1 class="m-0 text-dark">Dashboard</h1>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">