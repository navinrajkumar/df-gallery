<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<?php $server_url = base_url(); ?>
<h3 class="block_heading">generate the embed code for gallery: <?php echo $gallery->title; ?></h3>
	<script type="text/javascript">
	xml_url = '<?php echo $server_url . 'api/rest/get_gallery/'.$gallery->id;?>/json';
	function generateGallery(f){
			w = f['width'].value;
			h = f['height'].value;
			w = (w=='') ? '100%' : w;
			h = (h=='') ? '100%' : h;
			div = $('#generated-code');
			code = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" ';
			code +='width="'+w+'" height="'+h+'">';

			code +='\n\t<param name="allowFullScreen" value="true" />';
			code +='\n\t<param name="movie" value="<?php echo $server_url ?>DfGallery.swf" />';
			code +='\n\t<param name="quality" value="high" />';
			code +='\n\t<param name="FlashVars" value="xmlUrl='+xml_url+'" />';

			code +='\n\t<embed src="<?php echo $server_url ?>DfGallery.swf" quality="high"';
			code +='width="'+w+'" height="'+h+'" ';
			code +='FlashVars="xmlUrl='+xml_url+'" ';
			code +='allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
			code +='\n</object>';
			div.attr('value',code);
	}
	</script>
	<p class="h3_bg">Please enter the width and height to generate your &lt;embed&gt; code like youtube ... <br/>
	if you use pixels don't enter 500px, just enter 500.(u can also use %)
	</p>
	<form name="generate_form">
	<table width="26%" border="0" cellspacing="15" cellpadding="0">
	<tr>
		<td width="48%"><div align="right">Width</div></td>
		<td width="52%"><div align="right">
			<input name="width" type="text" maxlength="4"/>
		</div></td>
	</tr>
	<tr>
		<td><div align="right">Height</div></td>
		<td><div align="right">
			<input name="height" type="text" maxlength="4"/>
		</div></td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="right">
				<input type="button" onclick="generateGallery(this.form);" value=" Generate Code   "/>
				</div></td>
		</tr>
</table>
	<textarea cols="50" rows="12" id="generated-code"></textarea>
</form>
