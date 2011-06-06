<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<script type="text/javascript" src="<?php echo base_url() ?>/app/views/templates/ui/scripts/jquery_ajaxfileupload.js"></script>
<h3>Uploading images to <?php echo $album->title; ?></h3>
<script type="text/javascript">
var total_images = 0;
function add_new_image(){
	total_images++;
	$('<tr />')
	.attr('id','tr_image_'+total_images)
	.css({lineHeight:'20px'})
	.append (
		$('<td />')
		.attr('id','td_title_'+total_images)
		.css({paddingRight:'5px',width:'200px'})
		.append(
			$('<input type="text" />')
			.css({width:'200px'})
			.attr('id','input_title_'+total_images)
			.attr('name','input_title_'+total_images)
		)
	)
	.append (
		$('<td />')
		.attr('id','td_image_'+total_images)
		.css({width:'400px'})
		.append (
			$('<input type="file" />')
			.css({float:'left'})
			.attr('id','input_image_'+total_images)
			.attr('name','input_image_'+total_images)
		)
		.append (
			$('<span id="progress_'+total_images+'" class="padding5px"><a  href="#" onclick="$(\'#tr_image_'+total_images+'\').remove();" class="icon no_txt ico_delete">Remove field</a></span>')
		)
	)
	.appendTo('#table_container');
}
$(document).ready(function() {
	add_new_image();
});

function upload_next_image(){
	if(image_counter<total_images){
			image_counter++;
			value = $('#input_image_'+image_counter).attr('value');
			if(value == '' || value == undefined){
				$('#tr_image_'+image_counter).remove();
				upload_next_image();
			}else{
				title = $('#input_title_'+image_counter).attr('value');
				if (title == '') {
					title = $('#input_image_'+image_counter).attr('value');
					if(title.lastIndexOf('.')>-0){
						title = title.substring(0,title.lastIndexOf('.'));
					}
				}
				$('#td_title_'+image_counter).html(title);
				$('#progress_'+image_counter).html(' ');
				$('#progress_'+image_counter).css({float:'left'}).addClass('preloading');
			
				$.ajaxFileUpload
				({
					url:'<?php echo base_url() ?>admin/manage/upload_file/<? echo $album->id; ?>/'+image_counter,
					secureuri:false,
					fileElementId: "input_image_"+image_counter,
					dataType: 'json',
					title: title,
					success: function (data, status){
						if(typeof(data.error) != 'undefined'){
							if(data.error == ''){
								$('#td_image_'+image_counter).html(data.msg);
								upload_next_image();
							}else{
								$('#td_image_'+image_counter).html(data.error);
								upload_next_image();
							}
						}
					},
					error: function (data, status, e){
						$('#td_image_'+image_counter).html(e);
						upload_next_image();
					}
				});
			}
	}
}
function startUploads(){
	$('#start_upload_btn').hide();
	$('#add_more_fields').hide();
	image_counter = 0;
	upload_next_image();
}
</script>

<?php echo $error;?>
	<input type="hidden" name="total_images" id="total_images" value="0" />
	<div id="uploads">
		<table id="table_container">
		<tr>
			<td width="100px"><strong>Title</strong></td>
			<td ><strong>file</strong></td>
		</tr>
		</table>
		<br/>
		<input type="button" value="Add another field" id="add_more_fields" onclick="return add_new_image();" >
		<input type="button" value="Upload images" id="start_upload_btn" onclick="return startUploads();" >
	</div>
<br/>