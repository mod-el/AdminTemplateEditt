<style type="text/css">
	#login {
		margin: auto;
		max-width: 400px;
	}

	input {
		padding: 19px 15px !important;
	}

	button[type=submit] {
		background: #009fe8;
		color: #FFF;
	}

	button[type=submit]:focus {
		background: #00acf5;
		color: #FFF;
	}
</style>

<section class="container text-center py-5">
	<h1 class="pb-3"><?= APP_NAME ?></h1>
	<div style="margin: auto; width: 70%">
		<div class="red-message" id="login-error-message" style="display: none"></div>
	</div>
	<form action="?" method="post" id="login" onsubmit="login(); return false">
		<input type="text" class="form-control" name="username" placeholder="username" autofocus/><br/>
		<input type="password" class="form-control" name="password" placeholder="password"/><br/>
		<button type="submit" class="form-control" id="login-button">Login</button>
	</form>
</section>
