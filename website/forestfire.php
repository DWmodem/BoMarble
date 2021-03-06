<?php include('template.php');?>

<html>
	
	<head>
	<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<link href="../CSS/style.css" rel="stylesheet" type="text/css">
		<title>My first Three.js app</title>
		<style>
		#primaryCanvas {
			padding: 0px;
			margin: auto;
			background: black;
			width: 100%;

		}
		</style>
	</head>

	<body>
		<div class="container">
			<canvas id="primaryCanvas"></canvas> 
		</div>
		<div id="footer"></div>	

		<script src="../javascript/three.min.js"></script>
		<script src="../javascript/jquery-2.1.4.min.js"></script>
		<script src="../javascript/inty-js.js"></script>
		<script src="../javascript/forestfire/renderEngine.js"></script>
		<script src="../javascript/forestfire/forestFire.js"></script>

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
</html>
