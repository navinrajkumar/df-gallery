<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<h3 class="block_heading">Albums under <?php echo $gallery->title; ?></h3>
<p class="h3_bg">&nbsp;</p>
<?php if (sizeof($albums)>0){ ?>
<div class="clear">
	  <?php foreach ($albums as $album) {   ?>
	<div style="border-bottom:1px solid #EEE;margin-bottom:10px;padding:5px 5px 0 5px;">
		<div class="float" style="float:left;" >
			<?php if ($album->properties->get_value('album_type')=='custom'){ ?>
				<a href="<?php echo base_url(). 'admin/manage/index/'.$album->id ?>" class="content_title_link"><?php echo $album->title; ?></a>
			<?php }else { ?>
				<a href="<?php echo base_url(). 'admin/albums/edit/'.$album->id ?>" class="content_title_link"><?php echo $album->title; ?></a>
			<?php } ?>
		</div>
		<div style="float:right" >
		    <?php
		    	if ($album->properties->get_value('album_type')=='custom'){
		    		echo anchor('admin/manage/index/'.$album->id,'manage',array('class'=>'icon_txt ico_manage_photos'));
				}else{ ?>
					<div class="icon_txt" style="width:61px"></div>
				<?php }?>
			<?php echo anchor('admin/albums/edit/'.$album->id,'edit',array('class'=>'icon_txt ico_edit'));?></span>
	    	<?php echo anchor('admin/albums/properties/'.$album->id,'config',array('class'=>'icon_txt ico_configure'));?>
		    <a href="#" class="icon_txt ico_delete" onclick="redirectOnApproval('Do you want to delete the album : <?php echo $album->title; ?>','<?php echo base_url(); ?>admin/albums/delete/<?php echo $album->id; ?>');">delete</a>
		</div>
		<div class="clear"></div>
	</div>
  <?php } ?>
</div>
<?php } ?>
<?php echo anchor('/admin/albums/add/'.$gallery->id,'add albums',array('class'=>'button')); ?>