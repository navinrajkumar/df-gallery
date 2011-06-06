<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>

<h3>Importing files into : <?php echo $album->title; ?> from <?php echo $folder ?></h3>
<br/>
<div id="imports">
		<table id="table_container">
			<tr>
				<th width="150"><div align="left">file</div></th>
				<th><div align="left">state</div></th>
			</tr>
		</table>
		<br/>
</div>
<script type="text/javascript">
var files = [];
<?php foreach ($uploadable_files as $key => $file) {
	echo "\n files[$key]=[];";
	foreach ($file as $file_key=>$file_value) {
		echo "\n files[$key]['$file_key']='". $file_value."';";
	}
}?>
var i=0;
for(i=0;i<files.length;i++){
	var image_tr = $('<tr></tr>').css({lineHeight:'20px'});
	var img_loc = $('<td><input type="checkbox" checked="checked" id="file_check_'+i+'"/> &nbsp;&nbsp;'+files[i]['file']+'</td>');
	var img_status = $('<td id="file_status_'+i+'">&nbsp;</td>');
	image_tr.append(img_loc).append(img_status);
	image_tr.appendTo('#table_container');
}

image_counter = 0;
function import_next_image(){
	image_counter++;
	if(image_counter<files.length){
		if($('#edit-file_check_'+image_counter+':checked').val() !== null){
			data = "title="+files[image_counter]['title']+"&file="+files[image_counter]['file_uri']+"&img_name="+files[image_counter]['img_name'];
			$('#file_status_'+image_counter).html('');
			loading = $('<span></span>').addClass('preloading');;
			$('#file_status_'+image_counter).append(loading);
			$.ajax({
			   type: "POST",
			   url: '<?php echo base_url(); ?>admin/manage/import_file/<?php echo $album->id ?>',
			   data: data,
			   success: function(msg){
			   	 $('#file_status_'+image_counter).html(msg);
			   	 import_next_image();
			   }
			 });
		 }else{
		 	import_next_image();
		 }
	}
}
function start_import(){
	$('#start_upload_btn').css({display:'none'});
	$('#start_upload_btn').attr('disabled','disabled');
	image_counter = -1;
	import_next_image();
}
</script>
<div align="center">
	<input value="Start import" type="button" id="start_upload_btn" onclick="start_import();">
</div>