<div class="wrap">
	<h2>WP Plugin Template</h2>
	<form method="post" action="options.php"> 
		<?php @settings_fields('vf_users_list-group'); ?>
		<?php @do_settings_fields('vf_users_list-group'); ?>

		<?php do_settings_sections('vf_users_list'); ?>

		<?php @submit_button(); ?>
	</form>
</div>