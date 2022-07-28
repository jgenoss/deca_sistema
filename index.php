<?php
	session_start();
	require_once 'includes/Config.php';
if (config('system_active',true) == 'false') {
	echo '<center style="margin-top:30%;"><h1>ESTE SITIO WEB ESTA INACTIVO</h1></center>';
	exit();
}elseif (config('maintenance_page',true) == 'true') {
	include '500.php';
	exit();
}elseif (isset($_SESSION['START'])) {
	header('location: home.php');
}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php config('website_title'); ?></title>
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="assets/plugins/adminlte/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div id="login" class="login-box">
	<div class="lockscreen-logo">
		<img src="assets/img/logo.png" style="width:320px;" alt="deca">
	</div>
  <div class="card card-outline card-primary">
    <div class="card-body">
      <p class="login-box-msg">Inicia sesión</p>

      <form @submit.prevent="submitLogin">
        <div class="form-group mb-3">
          <input v-model="auth.user" style="text-align: center;" type="text" class="form-control" placeholder="Usuario" required>
        </div>
        <div class="form-group mb-3">
          <input v-model="auth.pass" style="text-align: center;" type="password" class="form-control" placeholder="Contraseña" required>
        </div>
				<div class="text-center">
					<button type="submit" class="btn btn-block bg-gradient-primary">Ingresar</button>
				</div>
      </form>
    </div>
  </div>
</div>
</body>
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/sweetalert2/sweetalert2.all.min.js" charset="utf-8"></script>
<script src="assets/plugins/adminlte/js/adminlte.min.js"></script>
<script src="assets/plugins/vue/vue.js"></script>
<script src="assets/plugins/axios/axios.min.js"></script>
<script src="assets/js/login.js" charset="utf-8"></script>
</body>
</html>
