<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Editing Album : <?php echo $album->title; ?></h3>
<p class="h3_bg">&nbsp;</p>
<?php echo form_open ( '/admin/albums/edit/'.$album->id ); ?>
<table border="0" cellspacing="10" cellpadding="5">
	<tr>
		<td width="100px">Album Title:</td>
		<td width="150px"><input type="text" maxlength="50" name="title" value="<?php echo  ($this->input->post ( 'title' ))? $this->input->post ( 'title' ) :  $this->album_bean->title;?>" /></td>
		<td width="450px">
		<div class="red"><?php echo $this->validation->title_error; ?></div>
		</td>
	</tr>
	
	<tr>
		<td width="100px">Type :</td>
		<td width="150px"><?php echo $album->properties->get_value('album_type'); ?></td>
		<td width="450px"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Save Changes" /></td>
	</tr>
</table>
</form>