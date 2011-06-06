<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>

<h2>Configure your gallery.</h2>
<p>Fill in these fields and we are ready to install.</p>
<?php
echo form_open ( '/admin/setup' )?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">hostname:</td>
		<td width="150px"><input
			title="header=[Hostname] body=[You most probably wont change this, unless you are connecting to a database on some other server.]"
			type="text" name="hostname"
			value="<?php
			echo $this->validation->hostname;
			?>" /></td>
		<td width="450px">
		<div class="red"><?php
		echo $this->validation->hostname_error;
		?></div>
		</td>
	</tr>

	<tr>
		<td>Database Type</td>
		<td><select name="dbdriver">
			<option value="mysql" selected="selected">MySQL</option>
			<option value="mysqli">MySQLi</option>
			<option value="postgre">Postgre</option>
		</select></td>
	</tr>

	<tr>
		<td>username</td>
		<td><input
			title="header=[Username] body=[The name of the user to connect to the database mentioned.]"
			type="text" name="username"
			value="<?php
			echo $this->validation->username;
			?>" /></td>
		<td>
		<div class="red"><?php
		echo $this->validation->username_error;
		?></div>
		</td>
	</tr>

	<tr>
		<td>password</td>
		<td><input type="password" name="password" value="" /></td>
		<td>
		<div class="red"><?php
		echo $this->validation->password_error;
		?></div>
		</td>
	</tr>

	<tr>
		<td>database</td>
		<td><input title="header=[Database] body=[The name of database.]"
			type="text" name="database"
			value="<?php
			echo $this->validation->database;
			?>" /></td>
		<td>
		<div class="red"><?php
		echo $this->validation->database_error;
		?></div>
		</td>
	</tr>

	<tr>
		<td>database prefix</td>
		<td><input
			title="header=[Database Prefix] body=[You can install multiple gallery applications in the same database with different prefixes.]"
			type="text" name="dbprefix"
			value="<?php
			echo $this->validation->dbprefix;
			?>" /></td>
		<td>
		<div class="red"><?php
		echo $this->validation->dbprefix_error;
		?></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Install the gallery" /></td>
	</tr>
</table>
</form>
