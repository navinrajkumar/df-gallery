<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>
<h2>Please Login</h2>
<?php
echo form_open ( '/admin/login' )?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">username:</td>
		<td width="150px"><input
			title="header=[Username] body=[please enter your login name.]"
			type="text" name="username"
			value="<?php echo $this->validation->username;?>" /></td>
		<td width="450px">
			<div class="red"><?php echo $this->validation->username_error; ?></div>
		</td>
	</tr>
	<tr>
		<td width="100px">password:</td>
		<td width="150px"><input
			title="header=[Password] body=[please enter your password for the login.<br/> Passwords are case sensitive.]"
			type="password" name="password"
			value="<?php echo $this->validation->password;?>" /></td>
		<td width="450px">
			<div class="red"><?php echo $this->validation->password_error; ?></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Login" /></td>
	</tr>
</table>
</form>