<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' ); ?>

<script type="text/javascript">
	function edit_image(id){
		var title=prompt("Please enter a new title",$('#image_title_'+id).html());
		if (title!=null && title!="") {
			$.ajax({
			   type: "POST",
			   url: '<?php echo base_url(); ?>admin/images/edit/'+id,
			   data: "title="+title,
			   success: function(msg){
			   	 if(msg == "1"){
			   	 	//alert("Updated");
			   	 	$('#image_title_'+id).html(title);
			   	 }else{
					alert("update failed, please try again.");		   	 	
			   	 }
			   }
			 });
		}
	}
</script>

<span class="padding5px"><?php echo anchor("/admin/manage/upload/$album->id",'Upload',array('class'=>'button')); ?></span>
<span class="padding5px"><?php echo anchor("/admin/manage/import/$album->id",'Import',array('class'=>'button')); ?></span>
<br/><br/>

<div style="margin-bottom:4px">
Pages : <?php
	$total_pages = ceil( $total_images / $images_per_page );
	for ($i=1;$i<=$total_pages;$i++) {?>
		<a href="<?php echo base_url(). "admin/manage/index/$album->id/$i/" ?>" class="breadcrumb"><?php echo $i; ?></a>
<?php } ?>
</div><hr/>
<div class="image_container">
	<?php foreach ($images as $image) { ?>
		<div class="img_placeholder" >
			<div class="img">
				<center>
					<div style="width:100px;height:75px;overflow:hidden;;">
					<img src="<?php echo UPLOAD_IMAGES_THUMBNAIL_FOLDER_URI . $image->properties->get_value('thumbnail_file'); ?>"/>
				</div>
				</center>
				<div id="image_title_<?php echo $image->id; ?>" class="padding5px" style="width: 100px; overflow-x: auto;height:30px"><?php echo $image->title; ?></div>
				<div class="blue_bg padding5px clear" style="height:16px;">
					<?php //echo anchor('admin/manage/edit_image/'.$album->id,'edit',array('class'=>'padding5px icon no_txt ico_edit'));?>
				    <a href="#" title="edit" style="padding-left:10px" class="icon no_txt ico_edit" onclick="edit_image(<?php echo $image->id; ?>);">edit</a>
		    		<a href="#" title="delete" style="padding-left:10px" class="icon no_txt ico_delete" onclick="redirectOnApproval('Do you want to delete the image : <?php echo $image->title; ?>','<?php echo base_url() ."admin/images/delete/$album->id/$current_page/$image->id"; ?>');">delete</a>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

