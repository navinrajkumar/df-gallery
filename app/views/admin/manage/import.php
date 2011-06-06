<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>

<?php echo form_open("/admin/manage/import/$album->id"); ?>
	<div class="properties">
		<h3>Import images into <?php echo $album->title; ?> </h3>
		<p>Please enter the absolute path of the images folder which contains the large images.<br/>
		DfGallery will import the large images and create thumbnails automatically.</p>
		<div id="property_container" style="clear: both; padding-top: 10px;">
			<div class="property">
				<span class="prop_title">Folder path</span>
				<input name="folder" class="prop_value" value="" type="text"/>
			</div>
			<div class="red"><?php echo $this->validation->folder_error; ?></div>
		</div>
	</div>
	<div align="center">
		<input value="Import" type="submit">
	</div>
</form>