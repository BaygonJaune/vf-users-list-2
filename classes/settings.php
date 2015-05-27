<?php
if(!class_exists('VF_Users_List_Settings'))
{
	class VF_Users_list_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
			add_action('admin_init', array(&$this, 'admin_init'));
//			add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// register your plugin's settings
			register_setting('vf_users_list-group', 'nbrows');
			register_setting('persons', 'nbrows-inside');
			//register_setting('vf_users_list-group', 'setting_b');

			// add your settings section
			add_settings_section(
				'vf_users_list-section', 
				__('Users List Settings','vf_users_list'), 
				array(&$this, 'settings_section_vf_users_list'), 
				'vf_users_list'
			);

			// add your setting's fields
			add_settings_field(
				'vf_users_list-nbrows', 
				__('NB Rows','vf_users_list'), 
				array(&$this, 'settings_field_select_options_text'), 
				'vf_users_list', 
				'vf_users_list-section',
				array(
					'field' => 'nbrows'
				)
			);

			// Possibly do additional admin_init tasks
		} // END public static function activate

		public function settings_section_vf_users_list()
		{
			// Think of this as help text for the section.
			//			echo 'These settings do things for the WP Plugin Template.';
		}

		/**
		 * This function provides text inputs for settings fields
		 */
		public function settings_field_input_text($args)
		{
			// Get the field name from the $args array
			$field = $args['field'];
			// Get the value of this setting
			$value = get_option($field);
			// echo a proper input type="text"
			echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
		} // END public function settings_field_input_text($args)



		public function settings_field_select_options_text($args)
		{
			// Get the field name from the $args array
			$field = $args['field'];
			// Get the value of this setting
			$value = get_option($field);
			// echo a proper input type="text"
			echo sprintf('<select name="%s" id="%s">',$field,$field);
			$steps = array('5','15','50') ;
			foreach($steps as $lstep) {
				($value == $lstep) ? $lselected = ' selected="selected" ' : $lselected = ' ' ;
				echo sprintf('<option value="%s" %s>%s</option>',$lstep,$lselected,$lstep) ;
			}
			echo '</select>' ;
		} // END public function settings_field_input_text($args)




		/**
		 * add a menu
		 */		
		public function add_menu()
		{
			// Add a page to manage this plugin's settings
			add_options_page(
				__('Users List Settings','vf_users_list'), 
				__('Users List','vf_users_list'), 
				'manage_options', 
				'vf_users_list', 
				array(&$this, 'plugin_settings_page')
			);
		} // END public function add_menu()

		/**
		 * Menu Callback
		 */		
		public function plugin_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/../templates/settings.php", dirname(__FILE__)));
		} // END public function plugin_settings_page()

		function admin_header() {
	
			error_log("le header");
	  		$page = ( isset($_REQUEST['page'] ) ) ? esc_attr( $_REQUEST['page'] ) : false;
	  		if( 'smc_users' != $page )
				return; 
			error_log("le header");
	
	  		echo '<style type="text/css">';
	  		echo '.wp-list-table .column-login_hash { width: 70px; }';
	  		echo '.wp-list-table .column-status { width: 70px; }';
	  		echo '.wp-list-table .column-sended_on { width: 110px; }';
	  		echo '.wp-list-table .manage-column.column-sended_on { background-color:  rgb(239, 239, 239); }';
	  		echo '.wp-list-table .manage-column.column-status { background-color: rgb(239, 239, 239); }';
	  		echo '.wp-list-table .manage-column.column-login_hash { background-color: rgb(239, 239, 239); }';
	  		echo '.wp-list-table .column-user_nicename { width: 15%; }';
	  		echo '.wp-list-table .column-user_email { width: 25%; }';
	  		echo '.wp-list-table .column-groups { width: 10%; }';
	  		echo '</style>';
		}
	


	} // END class WP_Plugin_Template_Settings
	
} // END if(!class_exists('WP_Plugin_Template_Settings'))
