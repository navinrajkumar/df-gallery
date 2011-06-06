<div style="padding:0 0 4px 0;border-bottom:1px solid #EEE;font-size:11px;">
<?php foreach ($breadcrumbs as $key => $breadcrumb) {
	if ($key!=0){echo '&raquo; ';}
	?>
	<a href="<?php echo $breadcrumb['url']; ?>" class="breadcrumb"><?php echo $breadcrumb['title']; ?></a>
<?php } ?>
</div>