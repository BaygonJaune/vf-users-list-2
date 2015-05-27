		<div class="wrap">
		
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
		
		
			<h2><?php _e('Persons', 'vf_users_list')?> 
		
		
		
				<!--a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=persons_form');?>"><?php _e('Add new', 'vf_users_menu')?></a-->
			</h2>
			<?php echo $message; ?>
			
			<form id="select-group" method="GET">
				<select name="group" id="group">
					<option value='-1'>tous</option>
					<?php 
			
						$list_groups = VF_Tools::get_groups_list(); 
						foreach ($list_groups as $key => $value) {
			
								if($key == $group)
									echo "<option selected='selected' value='$key'>$value</option>";
								else
									echo "<option value='$key'>$value</option>";
						}
					?>
				</select>
				<input name="doselect" type="submit" class="button action" value="selectionner">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			</form>
		
			<form id="persons-table" method="GET">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<input type="hidden" name="group" value="<?php echo $_REQUEST['group'] ?>"/>
				<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged'] ?>"/>
				<input type="hidden" name="orderby" value="<?php echo $_REQUEST['orderby'] ?>"/>
				<input type="hidden" name="order" value="<?php echo $_REQUEST['order'] ?>"/>
				<?php $table->display() ?>
			</form>
		
		</div>			
		
<div class="wrap">
	<form method="post" action="options.php"> 
		<?php @settings_fields('vf_users_list-group'); ?>
		<?php @do_settings_fields('vf_users_list-group'); ?>

		<?php do_settings_sections('vf_users_list'); ?>

		<?php @submit_button(); ?>
	</form>
</div>		