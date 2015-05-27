<?php 
if(!class_exists('VF_Tools'))
{
class VF_Tools {

	public static function get_groups_list(){
		global $wpdb;
		$table_groups = $wpdb->prefix .'uam_accessgroups'	;

		$groups = array();
	
		$lgroups = $wpdb->get_results( "SELECT ID,groupname FROM " . $table_groups . " ORDER BY ID", ARRAY_A );
		foreach ( $lgroups as $rgroup) {
			$groups[$rgroup['ID']] = $rgroup['groupname'] ;
		}
		return $groups ;
	}

	public static function get_group($group_id){
		global $wpdb;
		$table_groups = $wpdb->prefix .'uam_accessgroups'	;
	
		$groupname = NULL;
		if ($group_id !='') $groupname = $wpdb->get_var("SELECT groupname FROM " . $table_groups . " WHERE ID = $group_id");
	
		return is_null($groupname)?__('groupe inconnu','vf_users_list'):$groupname;	

	}

	public static function get_groups_from_user($user_id,$separator = ' '){
		global $wpdb;
		$table_groups = $wpdb->prefix .'uam_accessgroup_to_object'	;
	
		$groups = array() ;
		$list_groups = '';		
		$groups = $wpdb->get_col($wpdb->prepare("SELECT group_id FROM ".$table_groups." WHERE object_type =  'user' AND object_id =%d",$user_id));
		foreach ($groups as $lgroup) {
			if (!isset ($notfirst)) {$notfirst = false;} 
			else {$list_groups .= $separator ;} 
			$list_groups .= VF_Tools::get_group($lgroup) ;
		}
	
		return $list_groups ;
	}

	public static function add_group_to_user($user_id,$group_id){
		global $wpdb;
		$table_groups = $wpdb->prefix .'uam_accessgroup_to_object'	;	
	
		$nbraw = $wpdb->get_var("SELECT count(group_id) FROM $table_groups WHERE object_type =  'user' AND object_id = $user_id AND group_id = $group_id");
		if ($nbraw >0) {
			return 0;
		} else {
			$new_elem['group_id'] = $group_id ;
			$new_elem['object_type'] = 'user' ;
			$new_elem['object_id'] = $user_id ;
			
			$nbraw = $wpdb->insert($table_groups,$new_elem) ;
			return ($nbraw >0) ? 1 : 0 ;
		}
	}
	
	public static function get_groups(){
		$groups = array();
	
		global $wpdb;
	
		$lgroups = $wpdb->get_results(
			"SELECT ID,groupname
			FROM " . DB_ACCESSGROUP . "
			ORDER BY ID", ARRAY_A
		);
		foreach ( $lgroups as $rgroup) {
			$groups[$rgroup['ID']] = $rgroup['groupname'] ;
		}
		return $groups ;
	}
	public static function del_group_to_user($user_id,$group_id){
		global $wpdb;
		
		$table_groups = $wpdb->prefix .'uam_accessgroup_to_object'	;	
	
		$nbraw = $wpdb->get_var("SELECT count(group_id) FROM $table_groups WHERE object_type =  'user' AND object_id = $user_id AND group_id = $group_id");
		if ($nbraw =0) {
			return 0;
		} else {
			$del_elem['group_id'] = $group_id ;
			$del_elem['object_type'] = 'user' ;
			$del_elem['object_id'] = $user_id ;
			
			$nbraw = $wpdb->delete($table_groups,$del_elem) ;
			return ($nbraw >0) ? 1 : 0 ;
		}
	
		return 1;		
	}


	public static function get_lists_from_user($user_id,$separator=' '){
		global $wpdb;
	
		$table_user_list = $wpdb->prefix .'wysija_user_list'; 

	
		$groups = array() ;
		$list_lists = '';		
		$lists = $wpdb->get_col($wpdb->prepare("SELECT list_id FROM ".$table_user_list." WHERE unsub_date =  0 AND user_id =%d",$user_id));
		foreach ($lists as $llist) {
			if (!isset ($notfirst)) {$notfirst = false;} 
			else {$list_lists .= $separator ;} 
			$list_lists .= VF_Tools::get_list($llist) ;
		}
	
		return $list_lists ;
	}

	public static function get_list($list_id){
		global $wpdb;

		$table_list = $wpdb->prefix .'wysija_list'; 
	
		$listname = NULL;
		if ($list_id !='') $listname = $wpdb->get_var("SELECT name FROM " . $table_list . " WHERE list_id = $list_id");
	
		if (is_null($listname)) return 'pas de liste' ;
		return $listname ;
	}


	public static function get_lists_list(){
		$lists = array();
	
	
		global $wpdb;
	
		$table_list = $wpdb->prefix .'wysija_list'; 
		
		$llists = $wpdb->get_results(
			"SELECT list_id,name
			FROM " . $table_list . "
			ORDER BY name", ARRAY_A
		);
		foreach ( $llists as $rlist) {
			$lists[$rlist['list_id']] = $rlist['name'] ;
		}
		return $lists ;
	}


	public static function add_list_to_user($user_id,$list_id){
		global $wpdb;
		$table_user_list = $wpdb->prefix .'wysija_user_list';;	
	
		$nbraw = $wpdb->get_var("SELECT count(user_id) FROM $table_user_list WHERE user_id = $user_id AND list_id = $list_id");
		if ($nbraw >0) {
			return 0;
		} else {
			$new_elem['user_id'] = $user_id ;
			$new_elem['unsub_date'] = 0 ;
			$new_elem['sub_date'] = time();
			$new_elem['list_id'] = $list_id ;
			
			$nbraw = $wpdb->insert($table_user_list,$new_elem) ;
			return ($nbraw >0) ? 1 : 0 ;
		}
	}

	public static function del_list_to_user($user_id,$list_id){
		global $wpdb;
		$table_user_list = $wpdb->prefix .'wysija_user_list';;	
	
		$nbraw = $wpdb->get_var("SELECT count(user_id) FROM $table_user_list WHERE user_id = $user_id AND list_id = $list_id");
		if ($nbraw =0) {
			return 0;
		} else {
			$del_elem['list_id'] = $list_id ;
			$del_elem['user_id'] = $user_id ;
			
			$nbraw = $wpdb->delete($table_user_list,$del_elem) ;
			return ($nbraw >0) ? 1 : 0 ;
		}
	
		return 1;		
	}



	public static function detect_delimiter($file){
			$handle = @fopen($file, "r");
			$sumComma = 0;
			$sumSemiColon = 0;
			$sumBar = 0; 
		
			if($handle){
				while (($data = fgets($handle, 4096)) !== FALSE):
					$sumComma += substr_count($data, ",");
					$sumSemiColon += substr_count($data, ";");
					$sumBar += substr_count($data, "|");
				endwhile;
			}
			fclose($handle);
		
			if(($sumComma > $sumSemiColon) && ($sumComma > $sumBar))
				return ",";
			else if(($sumSemiColon > $sumComma) && ($sumSemiColon > $sumBar))
				return ";";
			else 
				return "|";
		}
		
	public static function string_conversion($string){
			if(!preg_match('%(?:
			[\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
			|\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
			|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
			|\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
			|\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
			|[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
			|\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
			)+%xs', $string)){
				return utf8_encode($string);
			}
			else
				return $string;
		}
	public static function get_roles($user_id){
			$roles = array();
			$user = new WP_User( $user_id );
		
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role )
					$roles[] = $role;
			}
		
			return $roles;
		}
		


}

}