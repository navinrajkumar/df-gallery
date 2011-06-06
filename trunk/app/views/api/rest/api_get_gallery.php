<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); 
echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>

<resp>
	<success>
		<gallery>
<?php 		foreach ($meta as $key => $value) { ?>
				<meta name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
<?php 		} ?>
			<config>
<?php 		foreach ($config['global'] as $key=> $value) { ?>
				<property name="<?php echo $key; ?>"><?php echo $value; ?></property>
<?php 		} ?>
				<theme>
<?php 		foreach ($config['theme'] as $key=> $value) { ?>
				<property name="<?php echo $key; ?>"><?php echo $value; ?></property>
<?php 		} ?>
				</theme>
				<skin>
<?php 		foreach ($config['skin'] as $key=> $value) { ?>
				<property name="<?php echo $key; ?>"><?php echo $value; ?></property>
<?php 		} ?>
				</skin>
			</config>
			<albums>
<?php 		foreach ($albums as $album) { ?>
				<album>
<?php 			if (isset($album->error)){ ?>
					<error code="<?php echo $album->error['code'] ?>"><?php echo $album->error['message']; ?></error>
<?php 			} else {
					foreach ($album->properties as $key => $value) { ?>
					<property name="<?php echo $key; ?>"><?php echo $value; ?></property>
<?php 				} ?>
					<images>
<?php 				foreach ($album->images as $image) { ?>
						<image>
<?php 					foreach ($image as $key => $value ) { ?>
							<property name="<?php echo $key; ?>"><?php echo $value; ?></property>
<?php 					} // end of foreach-property  ?>
						</image>
<?php 				}// end of foreach-image ?>
					</images>
<?php 			} ?>
				</album>
<?php 		} // end of foreach-album  ?>
			</albums>
		</gallery>
	</success>
</resp>