<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<?php echo form_open ( $form_action );?>
<div class="properties">
	<?php foreach ($properties_sections as $section) { ?>
		<h3><?php echo $section['name']; ?></h3>
		<p><?php echo $section['description']; ?></p>
		
		<div id="property_container" style="clear:both;padding-top:10px;">
		<?php foreach ($section['properties'] as $property) {
				$prop_name = $property['name'];
				$read_only = (isset($property['readonly']))? 'readonly="readonly"' : '';
				$prop_name_error = $prop_name . '_error';
				$current_value = (isset($_POST[$prop_name])) ? $_POST[$prop_name] : (isset($this->validation->$prop_name))? $this->validation->$prop_name : '';
				$type = (isset($property['type'])) ? $property['type'] : 'text';
				$validation_error = $this->validation->$prop_name_error;
				?>
				<div class="property<?php echo (isset($validation_error)) ? " message_red" : '';?>" >
					<span class="prop_title"><?php echo $property['display_name']; ?></span>
					
			<?php 	if ($type == 'textarea'){?>
					<textarea rows="3" cols="0" class="prop_value" name="<?php echo $prop_name; ?>" <?php echo $read_only ?>><?php 
						echo $current_value;
					?></textarea>
			<?php 	}elseif ($type == 'radio'){ 
						foreach ($property['values'] as $value) {
						?>
					<span style="padding-right:10px;">
						<input type="radio" name="<?php echo $prop_name; ?>" value="<?php echo $value; ?>" <?php echo ($current_value == $value) ? ' checked="checked"' : ''; ?> /> &nbsp;<?php echo $value; ?>
					</span>	
			<?php   	} 
					}elseif ($type == 'select'){ ?>
						<select name="<?php echo $prop_name; ?>">
						<?php foreach ($property['values'] as $value) { ?>
							<option value="<?php echo $value; ?>" <?php echo ($current_value == $value) ? ' selected="selected"' : ''; ?> >
								<?php echo $value; ?>
							</option>
							<?php } ?>
						</select>
			<?php }else { ?>
						<input name="<?php echo $prop_name; ?>" type="<?php echo $type; ?>" class="prop_value" value="<?php echo $this->validation->$prop_name; ?>" maxlength="255" <?php echo $read_only ?>></input>
			<?php }
				if (isset($this->validation->$prop_name_error)){ ?>
				  <div class="float red" style="padding:10px"><li style="list-style:circle"><?php echo $this->validation->$prop_name_error; ?><li></div>
			<?php } ?>
			</div>
		<?php
		// end  foreach $section 
		} ?>
		</div>
		<br/>
	<?php
	// end  foreach $properties_sections
	} ?>
</div>
<div align="center" class="clear">
	<input type="submit" value="<?php echo $submit_label; ?>" />
</div>
</form>