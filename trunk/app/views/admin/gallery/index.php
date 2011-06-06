<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Current Galleries</h3>
<p class="h3_bg">&nbsp;</p>
<?php if (sizeof($galleries)>0){ ?>
<div class="clear">
	  <?php foreach ($galleries as $gallery) {   ?>
	<div style="border-bottom:1px solid #EEE;margin-bottom:10px;padding:5px 5px 0 5px;">
		<div class="float" style="float:left;" ><?php echo anchor('/admin/albums/index/'.$gallery->id,$gallery->title,array('class'=>'content_title_link'));?></div>
		<div style="float:right" >
			<?php echo anchor('/admin/albums/index/'.$gallery->id,'albums',array('class'=>'icon_txt ico_manage_photos'));?></span>
			<?php echo anchor('/admin/gallery/edit/'.$gallery->id,'edit',array('class'=>'icon_txt ico_edit'));?></span>
	    	<?php echo anchor('/admin/gallery/properties/'.$gallery->id,'config',array('class'=>'icon_txt ico_configure'));?>
		    <?php echo anchor('/admin/gallery/generate/'.$gallery->id,'&lt;embed&gt;',array('class'=>'icon_txt ico_build'));?>
		    <a href="#" class="icon_txt ico_delete" onclick="redirectOnApproval('Do you want to delete the gallery : <?php echo $gallery->title; ?>','<?php echo base_url(); ?>/admin/gallery/delete/<?php echo $gallery->id; ?>');">delete</a>
		</div>
		<div class="clear"></div>
	</div>
  <?php } ?>
</div>
<?php } ?>
<?php echo anchor('/admin/gallery/add','Add Gallery',array('class'=>'button')); ?>