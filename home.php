<?php
session_start();
require_once 'includes/Config.php';

if (!isset($_SESSION['START'])) {
	unset($_SESSION['START']);
	session_destroy();
	header('location: index');
}
	$id_usuario = $_SESSION['START'][1];
?>
	<!DOCTYPE html>
	<html>
	<head>
	  <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <title><?php config('website_title'); ?> | <?php print(strtoupper($_GET['page']? $_GET['page']:"")); ?></title>
	  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
		<link rel="stylesheet" href="assets/plugins/sweetalert2/sweetalert2.min.css">
	  <link rel="stylesheet" href="assets/plugins/adminlte/css/adminlte.min.css">
		<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
		<link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
		<link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
		<link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
		<link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	</head>
	<style media="screen">
	.table td {
		padding: 2px;
		vertical-align: top;
		border-top: 1px solid #dee2e6;
	}
	</style>
	<body id="css" class="hold-transition sidebar-mini layout-fixed">
	  <div class="wrapper">
	    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
	      <!-- Left navbar links -->
	      <ul class="navbar-nav">
	        <li class="nav-item">
	          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	        </li>
	      </ul>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="fas fa-user"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
							<div class="dropdown"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-user mr-2"></i> Editar Perfil
							</a>
						</div>
					</li>
				</ul>
	    </nav>
	    <aside class="main-sidebar sidebar-dark-primary elevation-4">

				<div class="p-1 mt-3 mb-3">
					<a href="index3.html">
			      <img src="assets/img/logo.png" width="210px">
			    </a>
				</div>
	      <div class="sidebar">
	        <nav class="mt-2">
	          <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact text-sm" data-widget="treeview" role="menu" data-accordion="false">
							<?php
								foreach (permisos($id_usuario) as $row):?>
									<?php if ($row->mEstado == 1): ?>
										<?php if ($row->pVer == 1): ?>
											<li class="nav-item <?php (@$_GET['page'] == $row->mDescripcion)? 'menu-is-opening menu-open': ''?>">
												<a href="<?php echo $row->mEnlace ?>" class="nav-link <?php if(@$_GET['page'] == $row->mDescripcion) {echo 'active';}?>">
													<?php echo $row->mIcono ?>
													<p>
														<?php echo $row->mTitulo ?>
													</p>
												</a>
											</li>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach;?>
								<li class="nav-item">
                  <a id="logout" href="" class="nav-link btn-danger">
                    <i class="nav-icon fas fa-sign-out"></i>
										<p>
											Cerrar Sesion
										</p>
                  </a>
                </li>
	          </ul>
	        </nav>
	      </div>
	    </aside>
	    <div class="content-wrapper">
				<section class="content">
				  <div class="container-fluid">
						<?php
						if (isset($_SESSION['START'])) {
							foreach (permisos($id_usuario) as $row){
								if (@$_GET['page'] == $row->mDescripcion) {
									require_once 'vista/'.$row->mDescripcion.'.html';
								}
							}
						}
						?>
					</div>
				</section>
	    </div>
	    <footer class="main-footer">
	      <strong>Powered by <img src="assets/img/unnamedJG.png" width="40px" height="40px" alt="unnamedJG"></strong>
	    </footer>
	    <aside class="control-sidebar control-sidebar-dark">
	    </aside>
	  </div>
	</body>
	<script src="assets/plugins/jquery/jquery.min.js"></script>
	<script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
	<script src="assets/plugins/sweetalert2/sweetalert2.all.min.js" charset="utf-8"></script>
	<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="assets/plugins/adminlte/js/adminlte.js"></script>
	<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
	<script src="assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
	<script src="assets/plugins/select2/js/select2.full.min.js"></script>
	<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
	<script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
	<script src="assets/plugins/inputmask/jquery.inputmask.min.js"></script>
	<script src="assets/plugins/jszip/jszip.min.js"></script>
	<script src="assets/plugins/pdfmake/pdfmake.min.js"></script>
	<script src="assets/plugins/pdfmake/vfs_fonts.js"></script>
	<script src="assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
	<script src="assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
	<script src="assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
	<script src="assets/plugins/vue/vue.js"></script>
	<script src="assets/plugins/axios/axios.min.js"></script>
	<?php if (isset($_SESSION['START'])){
			foreach (permisos($id_usuario) as $row){
				if (@$_GET['page'] == $row->mDescripcion){
					echo '<script src="assets/js/'.$row->mDescripcion.'.js"></script>';
				}
			}
		}
	?>
	<script src="assets/js/home.js" charset="utf-8"></script>
	</html>
