<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Create a new Album under <?php echo $gallery->title; ?></h3>
<p class="h3_bg">&nbsp;</p>
<?php echo form_open ( '/admin/albums/add/'.$gallery->id )?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">Album Title:</td>
		<td width="150px"><input type="text" maxlength="50" name="title" value="<?php echo $this->validation->title;?>" /></td>
		<td width="450px">
		<div class="red"><?php echo $this->validation->title_error; ?></div>
		</td>
	</tr>
	<tr>
		<td width="100px">Type :</td>
		<td width="150px">
		<select name="album_type"  id="album_type">
			<?php
				foreach ($album_types as $key => $option) {
					$selected = '';
					if ($this->input->post('type')){
						if ($this->input->post('type')==$option){
							$selected = ' selected="selected"';
						}						
					}else{
						if ($key == 0){
							$selected = ' selected="selected"';
						}
					}
					echo "<option value='$option' $selected>$option</option>";
				}				
			?>
		</select>
		<td width="450px"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Create" /></td>
	</tr>
</table>
</form>