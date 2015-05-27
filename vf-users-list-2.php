<?php
/*
Plugin Name: VF Users List 2
Description: VF PlugIn SMC for WP / 
Plugin URI: http://www.smc.fr/
Author URI: http://www.smc.fr/
Author: Vincent Ferrari
License: GPL2
Version: 1.2
*/

require_once(sprintf("%s/classes/vf_users_list_table.php"	, dirname(__FILE__)));
require_once(sprintf("%s/classes/settings.php"				, dirname(__FILE__)));
require_once(sprintf("%s/classes/tools.php"				, dirname(__FILE__)));
require_once(sprintf("%s/classes/autologin.php"			, dirname(__FILE__)));


if(!class_exists('VF2_Users_List')){

class VF2_Users_List{
	
	public function __construct(){

		add_action('admin_init', array(&$this, 'admin_init'));
		add_action('admin_menu', array(&$this, 'add_menu'));
		add_action('init', array(&$this,'add_languages'));
		
		// Initialize Settings
		$VF2_Users_list_Settings = new VF_Users_List_Settings();
		$VF2_Users_List_AutoLogin = new VF_Auto_login();
		
		add_action( 'admin_head'	, array( &$VF2_Users_list_Settings , 'admin_header'   ));
		add_action('init'			, array( &$VF2_Users_List_AutoLogin,'val_triger_login'));
	}


	public static function activate(){
		$VF2_Users_List_AutoLogin.activate();		
	} 

	public static function deactivate(){
	} 

	public function admin_init(){
		$this->init_settings();
	} 
	
	public function init_settings(){
	}

	function add_menu() {
		add_menu_page(
						__('SMC - Utilisateurs', 'vf_users_list'),   	// page title
						__('Outils SMC', 'vf_users_list'),   			// menu title
						'list_users',				   					// capability
						'smc_users',    								// menu_lug 
						array(&$this, 'list_page')
		);
		
		add_submenu_page(
						'smc_users',
						__('SMC - Utilisateurs', 'vf_users_list'),   	// page title
						__('SMC - Utilisateurs', 'vf_users_list'),   	// menu title
						'list_users',				   					// capability
						'smc_users',    								// menu_lug 
						array(&$this, 'list_page')
		);
		
		add_submenu_page(
						'smc_users',
						__('SMC - Import', 'vf_users_list'),   		// page title
						__('SMC - Import', 'vf_users_list'),   		// menu title
						'list_users',				   					// capability
						'smc_import',    								// menu_lug 
						array(&$this, 'import_page')
		);
	}

	function add_languages(){
			load_plugin_textdomain('vf_users_list', false, dirname(plugin_basename(__FILE__)). '/languages');
	}




	private function import_users($file, $group){
			
		$wp_users_fields = array("user_nicename", "user_url", "display_name", "nickname", "first_name", "last_name", "description", "jabber", "aim", "yim", "user_registered");
		$wp_min_fields = array("Username", "Password", "Email");

			
		?>
		<div class="wrap">
			<h2><?php _e('Importations Des Utilisateurs','vf_users_list') ?></h2>	
			<?php
		set_time_limit(0);
		global $wpdb;
		$headers = array();
		//global $wp_users_fields;
		//global $wp_min_fields;	
		
		echo "<h3>".__('Enregistrement des Utilisateurs','vf_users_list')."</h3>";
		echo "<p>".__('La premiere ligne represente le format d\'importation','vf_users_list')."</p>";
		$row = 0;
		
		ini_set('auto_detect_line_endings',TRUE);
		$delimiter = VF_Tools::detect_delimiter($file);
		
		$manager = new SplFileObject($file);
		while ( $data = $manager->fgetcsv($delimiter) ):
			if( empty($data[0]) )
				continue;
		
			if( count($data) == 1 )
				$data = $data[0];
		
			foreach ($data as $key => $value) {
				$data[$key] = trim($value);
			}
		
			for($i = 0; $i < count($data); $i++){
				$data[$i] = VF_Tools::string_conversion($data[$i]);
			}
		
			if($row == 0):
				// check min columns username - password - email
				if(count($data) < 3){
					echo "<div id='message' class='error'>".__('Le fichier doit contenir 3 colonnes: username, password and email','vf_users_list')."</div>";
					break;
				}
		
				foreach($data as $element)
					$headers[] = $element;
		
				$columns = count($data);
		
				$headers_filtered = array_diff($headers, $wp_users_fields);
				$headers_filtered = array_diff($headers_filtered, $wp_min_fields);
				update_option("vf_custom_columns", $headers_filtered);
			?>
			<h3><php_e('Inserting and updating data','vf_users_list') ?></h3>
			<table>
				<tr><th>Row</th><?php foreach($headers as $element) echo "<th>" . $element . "</th>"; ?></tr>
			<?php
				$row++;
			else:
				if(count($data) != $columns): // if number of columns is not the same that columns in header
					echo '<script>alert("Ligne numero: ' . $row . ' n\'est pas au bon format, ignoree");</script>';
					continue;
				endif;
		
				$username = $data[0];
				$password = $data[1];
				$email = $data[2];
				$user_id = 0;
		
				if(username_exists($username)){
					$user_object = get_user_by( "login", $username );
					$user_id = $user_object->ID;
				} else {
					$user_id = wp_create_user($username, $password, $email);
				}
		
				if(is_wp_error($user_id)){
					echo '<script>alert("Probleme avec l\'utilisateur: ' . $username . ', ignore");</script>';
					continue;
				}
		
				if(!( in_array("administrator", VF_Tools::get_roles($user_id), FALSE) || is_multisite() && is_super_admin( $user_id ) )) {
					//	wp_update_user(array ('ID' => $user_id, 'role' => $role)) ;
		
					$wpdb->show_errors(); 
		
					$nb_group = $wpdb->get_var("SELECT COUNT(*) FROM " . DB_ACCESSGROUP_TO_OBJECT . " WHERE object_type = 'user' AND object_id = '".$user_id."' AND group_id = ".$group);
		
					if ($nb_group == 0)
		
					$wpdb->insert( 
						DB_ACCESSGROUP_TO_OBJECT, 
						array( 
							'object_type' => 'user',
							'object_id' => $user_id, 
							'group_id' => $group 
						), 
						array( 
							'%s',
							'%s', 
							'%d' 
						) 
					);
				}
		
				if($columns > 3){
					for($i=3; $i<$columns; $i++):
						if( !empty($data) ){
							if(in_array($headers[$i], $wp_users_fields))
								wp_update_user( array( 'ID' => $user_id, $headers[$i] => $data[$i] ) );
							else
								update_user_meta($user_id, $headers[$i], $data[$i]);
						}
					endfor;
				}
		
				// add main_group
				update_user_meta($user_id,'main_group',$group) ;
							
				echo "<tr><td>" . ($row - 1) . "</td>";
				foreach ($data as $element)
					echo "<td>$element</td>";
				echo "</tr>\n";
				flush();
			endif;
		
			$row++;						
		endwhile;
			?>
			</table>
			<br/>
			<p>Process finished you can go <a href="<?php echo get_admin_url() . '/users.php'; ?>">here to see results</a></p>
			<?php
			//fclose($manager);
			ini_set('auto_detect_line_endings',FALSE);
			?>
		</div>
		<?php
		}


	private function fileupload_process($group) {
		$uploadfiles = $_FILES['uploadfiles'];
	
	  	if (is_array($uploadfiles)) {
	
		foreach ($uploadfiles['name'] as $key => $value) {
	
		  // look only for uploded files
		  if ($uploadfiles['error'][$key] == 0) {
			$filetmp = $uploadfiles['tmp_name'][$key];
	
			//clean filename and extract extension
			$filename = $uploadfiles['name'][$key];
	
			// get file info
			// @fixme: wp checks the file extension....
			$filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
			$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
			$filename = $filetitle . '.' . $filetype['ext'];
			$upload_dir = wp_upload_dir();
	
			if ($filetype['ext'] != "csv") {
			  wp_die('File must be a CSV');
			  return;
			}
	
			// **
			// * Check if the filename already exist in the directory and rename the
			// * file if necessary
			// *
			$i = 0;
			while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
			  $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
			  $i++;
			}
			$filedest = $upload_dir['path'] . '/' . $filename;
	
			// **
			// * Check write permissions
			// *
			if ( !is_writeable( $upload_dir['path'] ) ) {
			  wp_die('Unable to write to directory. Is this directory writable by the server?');
			  return;
			}
	
			// **
			// * Save temporary file to uploads dir
			// *
			if ( !@move_uploaded_file($filetmp, $filedest) ){
			  wp_die("Error, the file $filetmp could not moved to : $filedest ");
			  continue;
			}
	
			$attachment = array(
			  'post_mime_type' => $filetype['type'],
			  'post_title' => $filetitle,
			  'post_content' => '',
			  'post_status' => 'inherit'
			);
	
			$attach_id = wp_insert_attachment( $attachment, $filedest );
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
	
			$this->import_users($filedest, $group);
		  }
		}
	  }
	}



	function list_page(){
		global $wpdb;
	
		$group = (isset($_REQUEST['group']) && $_REQUEST['group'] >=0) ? $_REQUEST['group'] : false ;
	
		$table = new VF_Users_List_Table();
		$nbrows=$table->prepare_items();
	
		$message = '';


		if ('delete_autologin' === $table->current_action()) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d on requested : %d', 'vf_list_users'), $nbrows,count($_REQUEST['id'])) . '</p></div>';
		}
		if ('create_autologin' === $table->current_action()) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items created: %d on requested : %d', 'vf_users_list'), $nbrows,count($_REQUEST['id'])) . '</p></div>';
		}
		if ('send_autologin' === $table->current_action()) {
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items sended: %d on requested : %d', 'vf_users_list'), $nbrows,count($_REQUEST['id'])) . '</p></div>';
		}

		if (null!==$table->current_action()) {
			$action = $table->current_action() ;
			$subaction = substr($action, 0, 10);
			if ($subaction == "add_group_") {
				$cgroup=substr($action, 10);
				$groupname = $table->get_group($cgroup);
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items added to %s : %d on requested : %d', 'vf_users_list'), $groupname,$nbrows,count($_REQUEST['id'])) . '</p></div>';
			}
		}

		if (null!==$table->current_action()) {
			$action = $table->current_action() ;
			$subaction = substr($action, 0, 10);
			if ($subaction == "del_group_") {
				$cgroup=substr($action, 10);
				$groupname = $table->get_group($cgroup);
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted to %s : %d on requested : %d', 'vf_users_list'), $groupname,$nbrows,count($_REQUEST['id'])) . '</p></div>';
			}
		}

		if (null!==$table->current_action()) {
			$action = $table->current_action() ;
			$subaction = substr($action, 0, 9);
			if ($subaction == "add_list_") {
				$clist=substr($action, 9);
				$listname = VF_Tools::get_list($clist);
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items added to %s : %d on requested : %d', 'vf_users_list'), $listname,$nbrows,count($_REQUEST['id'])) . '</p></div>';
			}
		}
		
		if (null!==$table->current_action()) {
			$action = $table->current_action() ;
			$subaction = substr($action, 0, 9);
			if ($subaction == "del_list_") {
				$clist=substr($action, 9);
				$listname = VF_Tools::get_list($clist);
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted to %s : %d on requested : %d', 'vf_users_list'), $listpname,$nbrows,count($_REQUEST['id'])) . '</p></div>';
			}
		}

		include(sprintf("%s/templates/main.php", dirname(__FILE__)));										 
	}

	function import_page(){ 
		
		if(isset($_REQUEST['uploadfile']))
			$this->fileupload_process($_REQUEST['group']);
		else
		{		
			include(sprintf("%s/templates/import.php", dirname(__FILE__)));
		}			
	}
	
}
}


$vf2_users_list = new VF2_Users_List();

register_activation_hook(__FILE__, array('VF_Auto_login', 'activate'));
register_deactivation_hook(__FILE__, array('VF_Auto_login', 'deactivate'));



