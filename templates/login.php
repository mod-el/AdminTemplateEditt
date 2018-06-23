<!DOCTYPE html>

<html>

<head>
	[:head]
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			background: #ededed !important;
			color: #FFF;
			font-size: 12px;
		}

		.red-message {
			padding: 10px;
			background: #FBB;
			border: solid #F33 1px;
			margin: 15px auto;
			font-weight: bold;
			color: #000;
		}

		.green-message {
			padding: 10px;
			background: #9F9;
			border: solid #080 1px;
			margin: 15px auto;
			font-weight: bold;
			color: #000;
		}

		#intestazione-cont {
			background: #009fe8;
		}

		#intestazione {
			margin: auto;
			padding-top: 10%;
		}

		#titolo {
			padding: 10px;
			border-bottom: solid #00b0e8 1px;
		}

		#firma {
			color: #dadad9;
			padding: 15px 0 25px;
			line-height: 16px;
		}

		#main {
			background: #ededed url('<?=PATH?>model/AdminTemplateEditt/files/img/bg-login.png') top center no-repeat;
			padding-top: 80px;
		}

		#login {
			margin: auto;
			max-width: 400px;
		}

		input {
			/*font-size: 25px;*/
			padding: 19px 15px !important;
			/*border: none;*/
			/*margin-top: 5px;*/
			/*box-sizing: border-box;*/
			/*text-align: left;*/
		}

		input[type=submit] {
			width: 100%;
			border: none;
			padding: 19px 15px;
			background: #009fe8;
			color: #FFF;
		}
	</style>
</head>

<body>
<div id="intestazione-cont">
	<header id="intestazione" class="container text-center">
		<h3 id="titolo" class="display-3">
			<?= APP_NAME ?>
		</h3>
		<div id="firma">
			<?php $config = $this->model->_Admin->retrieveConfig(); ?>
			<?= isset($config['stringaLogin1']) ? $config['stringaLogin1'] : '' ?><br/>
			<?= isset($config['stringaLogin2']) ? $config['stringaLogin2'] : '' ?>
		</div>
	</header>
</div>
<section id="main" class="container text-center">
	<div style="margin: auto; width: 70%">
		[:messages]
	</div>
	<form action="?" method="post" id="login">
		<input type="text" class="form-control" name="username" placeholder="username" autofocus/><br/>
		<input type="password" class="form-control" name="password" placeholder="password"/><br/>
		<input type="submit" value="Login"/>
	</form>
</section>
</body>

</html>