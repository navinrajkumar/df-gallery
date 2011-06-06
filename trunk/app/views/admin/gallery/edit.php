<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Editing Gallery : <?php echo $gallery->title; ?></h3>
<p class="h3_bg">&nbsp;</p>
<script type="text/javascript">
<?php
$selected_theme = $gallery->properties->get_value('theme');
$selected_skin = $gallery->properties->get_value('skin');

echo "var skins=[];\n";
foreach ( $themes as $theme ) {
	echo "skins['$theme[theme]'] = [];\n";
	foreach ( $theme ['skins'] as $key => $value ) {
		echo "skins['$theme[theme]'][$key] = '$value[file]';\n";
	}
} ?>
</script>
<?php echo form_open ( '/admin/gallery/edit/' . $gallery->id )?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">Gallery Title:</td>
		<td width="150px"><input type="text" maxlength="128" name="title" value="<?php 
			if (isset($_POST['title'])){
				echo $_POST['title'];
			}else {
				echo $gallery->title;
			}	
			?>" /></td>
		<td width="450px">
		<div class="red"><?php echo $this->validation->title_error; ?></div>
		</td>
	</tr>
	<tr>
		<td width="100px">Theme :</td>
		<td width="150px">
			<select name="theme" onchange="update_skins(this.id);" onclick="update_skins(this.id);" id="theme">
			<?php 
			
			foreach ( $themes as $theme ) { ?>
				<option value="<?php echo $theme ['theme']; ?>" <?php
				if ($_POST['theme'] == $theme['theme']){
					echo ' selected="selected"';
				}else {
					if ($selected_theme == $theme['theme']){
						echo ' selected="selected"';
					}
				}
				?> > <?php echo $theme ['theme']; ?></option>
				<?php } ?>
			</select>
		<td width="450px"></td>
	</tr>
	<tr>
		<td width="100px">Skin :</td>
		<td width="150px">
			<select name="skin" id="skin_sel">
			<?php 
			foreach ( $themes[$selected_theme]['skins'] as $key => $value ) { ?>
				<option value="<?php echo $value[file]; ?>" <?php
				if ($_POST['skin'] == $value[file]){
					echo ' selected="selected"';
				}else {
					if ($selected_skin == $value[file]){
						echo ' selected="selected"';
					}
				}
				?> > <?php echo $value[file]; ?></option>
			<?php } ?>
		</select>
		<td width="450px"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Save Changes" /></td>
	</tr>
</table>
</form>