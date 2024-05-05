<style>
	table {
		width: 100%;
	}

	td {
		padding: 5px;
	}

	td:nth-child(odd) {
		text-align: right;
	}

	td:nth-child(even) {
		text-align: left;
	}
</style>

<h2>Admin Template "Editt" settings</h2>

<form action="" method="post" name="configForm">
	<hr/>
	<table>
		<tr>
			<td>Background header</td>
			<td>
				<input type="color" name="background-header" value="<?= $config['background-header'] ?>"/>
			</td>
			<td>Testo header</td>
			<td>
				<input type="color" name="text-header" value="<?= $config['text-header'] ?>"/>
			</td>
		</tr>
		<tr>
			<td>Tasti menù - Spenti - Background</td>
			<td>
				<input type="color" name="background-menu-primary-off" value="<?= $config['background-menu-primary-off'] ?>"/>
			</td>
			<td>Testo</td>
			<td>
				<input type="color" name="text-menu-primary-off" value="<?= $config['text-menu-primary-off'] ?>"/>
			</td>
			<td>Accesi - Background</td>
			<td>
				<input type="color" name="background-menu-primary-on" value="<?= $config['background-menu-primary-on'] ?>"/>
			</td>
			<td>Testo</td>
			<td>
				<input type="color" name="text-menu-primary-on" value="<?= $config['text-menu-primary-on'] ?>"/>
			</td>
		</tr>
		<tr>
			<td>Sottovoci menù - Spente - Background</td>
			<td>
				<input type="color" name="background-menu-secondary-off" value="<?= $config['background-menu-secondary-off'] ?>"/>
			</td>
			<td>Testo</td>
			<td>
				<input type="color" name="text-menu-secondary-off" value="<?= $config['text-menu-secondary-off'] ?>"/>
			</td>
			<td>Accese - Background</td>
			<td>
				<input type="color" name="background-menu-secondary-on" value="<?= $config['background-menu-secondary-on'] ?>"/>
			</td>
			<td>Testo</td>
			<td>
				<input type="color" name="text-menu-secondary-on" value="<?= $config['text-menu-secondary-on'] ?>"/>
			</td>
		</tr>
	</table>

	<p>
		<input type="submit" value="Save"/>
	</p>
</form>
