<?php


/*
 * 305752cb70d870f26c811a7b562538b6
 *  http://apptest.groupe-credit-du-nord.com/les-rdv-smc/?hash=04ecbafefa5cf25b7a471dfeb04fff69
 * 
 * http://asterix.smc/~vinnyf/wp4/wp-admin/admin.php?page=smc_users
 * 
 * http://asterix.smc/~vinnyf/wp4/?hash=305752cb70d870f26c811a7b562538b6
 * 
 */

if(!class_exists('VF_Auto_login'))
{
	class VF_Auto_login
	{
		
		public static function activate(){
			global $wpdb;
		
			$sql = "
			--
			-- Table structure for table `".$wpdb->prefix."vf_auto_login`
			--
		
			CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."vf_auto_login` (
			  `uid` int(10) NOT NULL AUTO_INCREMENT,
			  `username` varchar(255) NOT NULL,
			  `login_hash` varchar(255) NOT NULL,
			  `status` tinyint(2) NOT NULL DEFAULT '1',
			  `date` int(10) NOT NULL,
			  `sended_on` datetime,
		  	PRIMARY KEY (`uid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
			$wpdb->query($sql);
//			add_option('val_version', val_version);
		} 

		public static function deactivate(){
			global $wpdb;

		//		$sql = "DROP TABLE ".$wpdb->prefix."vf_auto_login;";
		//		$wpdb->query($sql);
		
//				delete_option('val_version'); 
		} 



		function val_triger_login(){
			global $wpdb;
		
		
			error_log("val triger login") ;
		
			@$login_hash = $this->val_sanitize_variables($_GET['hash']);
			$query = "SELECT * FROM ".$wpdb->prefix."vf_auto_login WHERE `login_hash` = '".$login_hash."' AND `status` = 1";
			$result = $this->val_selectquery($query);
			$username = $result['username'];
		
			if(!is_user_logged_in() && !empty($username)){
				// What is the user id ?
				$user = get_userdatabylogin($username);
				$user_id = $user->ID;
		
				// Lets login
				wp_set_current_user($user_id, $username);
				wp_set_auth_cookie($user_id,true);
				do_action('wp_login', $username);
		
				error_log("val triger login : OK !!!") ;
				wp_redirect("http://www.example.com");
				exit;
			}
		}
		
		function val_selectquery($query){
			global $wpdb;
		
			$result = $wpdb->get_results($query, 'ARRAY_A');
			return current($result);
		}

		function val_sanitize_variables($variables = array()){
		
			if(is_array($variables)){
				foreach($variables as $k => $v){
					$variables[$k] = trim($v);
					$variables[$k] = escapeshellcmd($v);
					$variables[$k] = mysql_real_escape_string($v);
				}
			}else{
				$variables = mysql_real_escape_string(escapeshellcmd(trim($variables)));
			}
		
			return $variables;
		}

		function val_valid_ip($ip){
		
				if(!ip2long($ip)){
					return false;
				}	
				return true;
			}
		
			function val_is_checked($post){
		
				if(!empty($_POST[$post])){
					return true;
				}	
				return false;
			}
		
			function val_report_error($error = array()){
		
				if(empty($error)){
					return true;
				}
		
				$error_string = '<b>Please fix the below errors :</b> <br />';
		
				foreach($error as $ek => $ev){
					$error_string .= '* '.$ev.'<br />';
				}
		
				echo '<div id="message" class="error"><p>'
								. __($error_string, 'vf-auto-login')
								. '</p></div>';
			}
		
			function val_objectToArray($d){
			  if(is_object($d)){
				$d = get_object_vars($d);
			  }
		
			  if(is_array($d)){
				return array_map(array(&$this,'val_objectToArray'), $d); // recursive
			  }elseif(is_object($d)){
				return val_objectToArray($d);
			  }else{
				return $d;
			  }
			}
		
		
		
			function delid($pdelid) {
		
			}
		
			public static function sendid($psendid){
				global $wpdb;
		
				$sendid = (int) $this->val_sanitize_variables($psendid);
				$_sended_on = current_time( 'mysql' );
				$wpdb->query("UPDATE ".$wpdb->prefix."vf_auto_login SET `sended_on` = '".$_sended_on."' WHERE `uid` = ".$sendid);		
		
		
				/* sending the E-mail */
				$to = 'vincent.ferrari@chezlesmonstres.net';
				$subject = 'connexion au site';
				$body = 'le corps du message';
				$headers = array('Content-type: text/html; charsetUTF-8');
		
				wp_mail($to,$subject,$body,$headers);
		
			}
		
			public static function enable_autologin($userlogin){
				global $wpdb;
				$table_autologin = $wpdb->prefix.'vf_auto_login';
				$uid = $wpdb->get_var( "SELECT uid FROM $table_autologin where username= '$userlogin'" );
				error_log("send uid : $uid");
				if (!$uid) {
					return 0 ;
				} else {	
					$_sended_on = current_time( 'mysql' );
					$wpdb->query("UPDATE ".$table_autologin." SET status = 1 WHERE uid = ".$uid);		
					return 1;
				}
			}
		
			public static function disable_autologin($userlogin){
				global $wpdb;
				$table_autologin = $wpdb->prefix.'vf_auto_login';
				$uid = $wpdb->get_var( "SELECT uid FROM $table_autologin where username= '$userlogin'" );
				error_log("send uid : $uid");
				if (!$uid) {
					return 0 ;
				} else {	
					$_sended_on = current_time( 'mysql' );
					$wpdb->query("UPDATE ".$table_autologin." SET status = 0 WHERE uid = ".$uid);		
					return 1;
				}
			}
		
		
			public static function send_autologin($userlogin){
				global $wpdb;
				$table_autologin = $wpdb->prefix.'vf_auto_login';
				$uid = $wpdb->get_var( "SELECT uid FROM $table_autologin where username= '$userlogin'" );
				error_log("send uid : $uid");
				if (!$uid) {
					return 0 ;
				} else {	
					$_sended_on = current_time( 'mysql' );
					$wpdb->query("UPDATE ".$table_autologin." SET sended_on = '".$_sended_on."' WHERE uid = ".$uid);	
		
					$login_hash = $wpdb->get_var( "SELECT login_hash FROM $table_autologin where username= '$userlogin'" );
					$siteurl = get_option('siteurl');
					$login_url = $siteurl.'/?hash='.$login_hash;
		
		
				/* sending the E-mail */
				$to = 'vincent.ferrari@chezlesmonstres.net';
				$subject = 'connexion au site';
				$body = 'le corps du message';
				$headers = array('Content-type: text/html; charsetUTF-8');
		
				$body .='<br /><a href="'.$login_url.'"> Votre autologin '.$login_url.' </a><br /><i><small>la SMC</small></i>';
		
				wp_mail($to,$subject,$body,$headers);
					return 1;
				}
			}
		
		
		
			public static function create_autologin($userlogin){
					global $wpdb;
					$table_autologin = $wpdb->prefix.'vf_auto_login';
					$exists = $wpdb->get_var( "SELECT COUNT(uid) FROM $table_autologin where username= '$userlogin'" );
								if ($exists > 0) {
						return 0 ;
					} else {	
						error_log("create_autologin:$userlogin");
						$options['username'] = $userlogin;
						$options['login_hash'] = md5(uniqid($options['username'], true));
						$options['status'] = 1;
						$options['date'] = date('Ymd');
		
						return $wpdb->insert($wpdb->prefix.'vf_auto_login', $options);
					}	
			}
		
			public static function delete_autologin($userlogin){
						global $wpdb;		
						$table_autologin = $wpdb->prefix.'vf_auto_login';
						return $wpdb->delete($wpdb->prefix.'vf_auto_login', array( 'username' => $userlogin));
				}
		
			function createid($login_options) {
				// error array 
				// message array
		
		
				$user = get_user_by('login', $vf_auto_login_options['username']);
		
				if(empty($user)){
					$error[] = 'The username does not exist.';
				}
		
				if(empty($error)){
		
					$options['username'] = $vf_auto_login_options['username'];
					$options['login_hash'] = md5(uniqid($options['username'], true));
					$options['status'] = ($this->val_is_checked('status') ? 1 : 0);
					$options['date'] = date('Ymd');
		
					$wpdb->insert($wpdb->prefix.'vf_auto_login', $options);
		
					if(!empty($wpdb->insert_id)){
						$query = "SELECT * FROM ".$wpdb->prefix."vf_auto_login WHERE `uid` = '".$wpdb->insert_id."'";
						$result = $this->val_selectquery($query);
						$login_hash = $result['login_hash'];
						$login_url = $siteurl.'/?hash='.$login_hash;
		
		
		
						$message =  __('Login URL added successfully. You can use the following Login URL: <br />
									<a href="'.$login_url.'">'.$login_url.'</a>', 'vf-auto-login');
					}else{
						$message =  __('There were some errors while adding Login URL', 'vf-auto-login') ;			
					}
		
				}else{
					$this->val_report_error($error);
				}
		
		
			}

	}
}
