<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Create a new Gallery</h3>
<p class="h3_bg">&nbsp;</p>
<script type="text/javascript">
<?php
echo "var skins=[];\n";
foreach ( $themes as $theme ) {
	echo "skins['$theme[theme]'] = [];\n";
	foreach ( $theme ['skins'] as $key => $value ) {
		echo "skins['$theme[theme]'][$key] = '$value[file]';\n";
	}
}
?>
</script>
<?php echo form_open ( '/admin/gallery/add' )?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">Gallery Title:</td>
		<td width="150px"><input type="text" maxlength="128" name="title" value="<?php echo $this->validation->title;?>" /></td>
		<td width="450px">
		<div class="red"><?php echo $this->validation->title_error; ?></div>
		</td>
	</tr>
	<tr>
		<td width="100px">Theme :</td>
		<td width="150px">
			<select name="theme" onchange="update_skins(this.id);" onclick="update_skins(this.id);" id="theme" >
				<?php foreach ( $themes as $theme ) { ?>
				<option value="<?php echo $theme ['theme']; ?>" <?php 
				echo $this->validation->set_select ( 'theme', $theme ['theme'] );
				?> > <?php echo $theme ['theme']; ?></option>
				<?php } ?>
			</select>
		<td width="450px"></td>
	</tr>
	<tr>
		<td width="100px">Skin :</td>
		<td width="150px">
		<select name="skin" id="skin_sel">
			<option value="standard.png">standard.png</option>
		</select>
		<td width="450px"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Create" /></td>
	</tr>
</table>
</form>