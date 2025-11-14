<style>
	body {
		background: #f5f6fa;
		display: flex;
		align-items: center;
		justify-content: center;
		min-height: 100vh;
		margin: 0;
		padding: 0;
	}

	#login-cont {
		max-width: 450px;
		width: 100%;
		padding: 20px;
	}

	.login-card {
		background: white;
		border-radius: 12px;
		box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
		padding: 40px;
		text-align: center;
	}

	.login-logo {
		margin-bottom: 30px;
	}

	.login-logo img {
		max-height: 80px;
		max-width: 100%;
	}

	.login-title {
		font-size: 1.75rem;
		font-weight: 600;
		color: #2c3e50;
		margin-bottom: 8px;
	}

	.login-subtitle {
		font-size: 0.95rem;
		color: #7f8c8d;
		margin-bottom: 30px;
	}

	.form-group {
		text-align: left;
		margin-bottom: 20px;
	}

	.form-group label {
		display: block;
		font-size: 0.9rem;
		font-weight: 500;
		color: #2c3e50;
		margin-bottom: 8px;
	}

	.input-wrapper {
		position: relative;
	}

	.input-wrapper .input-icon {
		position: absolute;
		left: 15px;
		top: 50%;
		transform: translateY(-50%);
		color: #7f8c8d;
		font-size: 1.1rem;
	}

	.form-group input {
		width: 100%;
		padding: 14px 15px !important;
		padding-left: 45px !important;
		border: 1px solid #dfe4ea;
		border-radius: 8px;
		font-size: 0.95rem;
		transition: all 0.3s ease;
		box-sizing: border-box;
	}

	.form-group input:focus {
		outline: none;
		border-color: var(--background-menu-primary-on);
		box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
	}

	button[type=submit] {
		width: 100%;
		background: var(--background-menu-primary-on);
		color: #FFF;
		padding: 14px 20px;
		border: none;
		border-radius: 8px;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		margin-top: 10px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	button[type=submit]:hover {
		background: var(--background-menu-secondary-on);
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
	}

	button[type=submit]:active {
		transform: translateY(0);
	}

	.red-message {
		background: #fee;
		color: #c33;
		padding: 12px;
		border-radius: 8px;
		margin-bottom: 20px;
		font-size: 0.9rem;
	}
</style>

<section class="container" id="login-cont">
	<?php
	if (file_exists(INCLUDE_PATH . 'app' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) {
		?>
		<div class="text-center login-logo">
			<img src="<?= PATH ?>app/assets/img/logo.png" alt="Logo"/>
		</div>
		<?php
	}
	?>

	<div class="login-card">
		<h2 class="login-title">Login</h2>
		<p class="login-subtitle">Inserisci username e password</p>

		<div id="login-loading" style="display: none"><img src="<?= PATH ?>model/Output/files/loading.gif" alt="Attendere..."/></div>
		<form action="?" method="post" id="login" onsubmit="login(); return false">
			<div class="red-message" id="login-error-message" style="display: none"></div>

			<div class="form-group">
				<label for="username">Username</label>
				<div class="input-wrapper">
					<span class="input-icon">@</span>
					<input type="text" class="form-control" name="username" id="username" placeholder="username" autofocus/>
				</div>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<div class="input-wrapper">
					<span class="input-icon">🔒</span>
					<input type="password" class="form-control" name="password" id="password" placeholder="password"/>
				</div>
			</div>

			<button type="submit" id="login-button">Login</button>
		</form>
	</div>
</section>
