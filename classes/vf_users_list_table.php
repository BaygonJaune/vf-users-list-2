<?php
/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
 
 
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if(!class_exists('VF_Tools')){
	require_once(sprintf("%s/../classes/tools.php", dirname(__FILE__)));
} 
class VF_Users_List_Table extends WP_List_Table {	
	/**
	 * [REQUIRED] You must declare constructor and give some basic params
	 */
	function __construct(){
		global $status, $page;

		parent::__construct(array(
			'singular' => 'utilisateur SMC',
			'plural' => 'utilisateurs SMC',
		));
		
	}


	/**
	 * [REQUIRED] this is a default column renderer
	 *
	 * @param $item - row (key, value array)
	 * @param $column_name - string (key)
	 * @return HTML
	 */
	function column_default($item, $column_name){
		return $item[$column_name];
	}

	/**
	 * [OPTIONAL] this is example, how to render specific column
	 *
	 * method name must be like this: "column_[column_name]"
	 *
	 * @param $item - row (key, value array)
	 * @return HTML
	 */
	function column_sended_on($item){
		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'])) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_nicename';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
		$group = isset($_REQUEST['group']) ? $_REQUEST['group'] : -1 ;

		if (isset($item['login_hash']))
		return isset($item['sended_on'])?'<em>' . $item['sended_on'] . '</em>':
		'<a class="submitdelete button action" href="admin.php?page=smc_users&paged='.$paged
		.'&order='.$order
		.'&orderby='.$orderby
		.'&group='.$group
		.'&action=send_autologin&id='
		.$item['ID'].'" onclick="return confirm(\''.addslashes(__('Are you sure you want to send this Login URL ?','vf_users_list')).'\')">'.__('Send','vf_users_list').'</a>&nbsp;&nbsp;';
		else return '';
	}

	function column_status($item){
		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'])) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_nicename';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
		$group = isset($_REQUEST['group']) ? $_REQUEST['group'] : -1 ;

		
		
		if (isset($item['login_hash']))
		return (isset($item['status']) & ($item['status']==1))
		? '<a class="submitdelete button action" href="admin.php?page=smc_users&paged='.$paged.'&order='.$order.'&orderby='.$orderby.'&group='.$group.'&action=disable_autologin&id='.$item['ID']
		.'" onclick="return confirm(\''.addslashes(__('Are you sure you want to disable this Login URL ?','vf_users_list')).'\')">'.__('Disable','vf_users_list').'</a>&nbsp;&nbsp;' 
		: '<a class="submitdelete button action" href="admin.php?page=smc_users&paged='.$paged.'&order='.$order.'&orderby='.$orderby.'&group='.$group.'&action=enable_autologin&id='.$item['ID']
		.'" onclick="return confirm(\''.addslashes(__('Are you sure you want to enable this Login URL ?','vf_users_list')).'\')">'.__('Enable','vf_users_list').'</a>&nbsp;&nbsp;';
		else return '';
	}
	
	function column_login_hash($item){
		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'])) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_nicename';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
		$group = isset($_REQUEST['group']) ? $_REQUEST['group'] : -1 ;

		return isset($item['login_hash']) 
		? '<a class="submitdelete button action" href="admin.php?page=smc_users&paged='.$paged.'&order='.$order.'&orderby='.$orderby.'&group='.$group.'&action=delete_autologin&id='.$item['ID']
		.'" onclick="return confirm(\''.addslashes(__('Are you sure you want to delete this Login URL ?','vf_users_list')).'\')">'.__('Delete','vf_users_list').'</a>&nbsp;&nbsp;' 
		: '<a class="submitdelete button button-primary" href="admin.php?page=smc_users&paged='.$paged.'&order='.$order.'&orderby='.$orderby.'&group='.$group.'&action=create_autologin&id='.$item['ID'].''
		.'" onclick="return confirm(\''.addslashes(__('Are you sure you want to create this Login URL ?','vf_users_list')).'\')">'.__('Create','vf_users_list').'</a>&nbsp;&nbsp;';
	}


	/**
 	* [REQUIRED] this is how checkbox column renders
 	*
 	* @param $item - row (key, value array)
 	* @return HTML
 	*/
	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['ID']
		);
	}

	/**
	 * [REQUIRED] This method return columns to display in table
	 * you can skip columns that you do not want to show
	 * like content, or description
	 *
	 * @return array
	 */
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'user_nicename' => __('Name', 'vf_users_list'),
			'user_email' => __('E-Mail', 'vf_users_list'),
			'groups' => __('Groups', 'vf_users_list'),
			'login_hash' => __('Login','vf_users_list'),
			'status' => __('Status','vf_users_list'),
			'sended_on' => __('Sended on','vf_users_list'),
			'lists' => __('Mailing Lists','vf_users_list'),
		);
		return $columns;
	}
	/**
	 * [OPTIONAL] This method return columns that may be used to sort table
	 * all strings in array - is column names
	 * notice that true on name column means that its default sort
	 *
	 * @return array
	 */
	function get_sortable_columns(){
		$sortable_columns = array(
			'user_nicename' => array('user_name', true),
			'user_email' => array('user_email', false),
			'sended_on' => array('sended_on', false),
		);
		return $sortable_columns;
	}



	/**
	 * [OPTIONAL] Return array of bult actions if has any
	 *
	 * @return array
	 */
	function get_bulk_actions()
	{
		$actions = array(
			'create_autologin' => 'Create Auto Login',
			'delete_autologin' => 'Delete Auto Login',
			'send_autologin' => 'Send Auto Login'
		);
		$groups = VF_Tools::get_groups_list();
		foreach ($groups as $key=>$value) {
			$actions['add_group_'.$key] = __('add to group','vf_users_list').' '.$value ;
		}
		foreach ($groups as $key=>$value) {
			$actions['del_group_'.$key] = __('remove to group','vf_users_list').' '.$value ;
		}

		$lists = VF_Tools::get_lists_list();
		foreach ($lists as $key=>$value) {
			$actions['add_list_'.$key] = __('add to list','vf_users_list').' '.$value ;
		}
		foreach ($lists as $key=>$value) {
			$actions['del_list_'.$key] = __('remove to list','vf_users_list').' '.$value ;
		}


		return $actions;
	}
	
	/**
	 * [OPTIONAL] This method processes bulk actions
	 * it can be outside of class
	 * it can not use wp_redirect coz there is output already
	 * in this example we are processing delete action
	 * message about successful deletion will be shown on page in next part
	 */
	function process_bulk_action()
	{
		global $wpdb;
		$nbrows = 0 ;

		if ('send_autologin' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if (is_array($ids)) 
				foreach ($ids  as $id){ $nbrows += $this->process_send_action($id) ; }
			else $nbrows += $this->process_send_action($ids);	
		}
		if ('create_autologin' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if (is_array($ids)) 
				foreach ($ids  as $id){ $nbrows += $this->process_create_action($id) ; }
			else $nbrows += $this->process_create_action($ids);	
		}
		if ('delete_autologin' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if (is_array($ids)) 
				foreach ($ids  as $id){ $nbrows += $this->process_delete_action($id) ; }	
			else $nbrows += $this->process_delete_action($ids) ;	
		}
		if ('disable_autologin' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if (is_array($ids)) 
				foreach ($ids  as $id){ $nbrows += $this->process_disable_action($id) ; }	
			else $nbrows += $this->process_disable_action($ids) ;	
		}
		if ('enable_autologin' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if (is_array($ids)) 
			
	
				foreach ($ids  as $id){ $nbrows += $this->process_enable_action($id) ; }	
			else $nbrows += $this->process_enable_action($ids) ;	
		}

		if (null!==$this->current_action()) {
			$action = $this->current_action() ;
			$subaction = substr($action, 0, 10);
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if ($subaction == "add_group_") {
				$cgroup=substr($action, 10);
				if (is_array($ids)) 
					foreach ($ids  as $id){ $nbrows += $this->process_addgroup_action($id,$cgroup) ; }	
				else $nbrows += $this->process_addgroup_action($ids,$cgroup) ;	
				}
			
		}		

		if (null!==$this->current_action()) {
			$action = $this->current_action() ;
			$subaction = substr($action, 0, 10);
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if ($subaction == "del_group_") {
				$cgroup=substr($action, 10);
				if (is_array($ids)) 
					foreach ($ids  as $id){ $nbrows += $this->process_delgroup_action($id,$cgroup) ; }	
				else $nbrows += $this->process_delgroup_action($ids,$cgroup) ;	
				}
			
		}		


		if (null!==$this->current_action()) {
			$action = $this->current_action() ;
			$subaction = substr($action, 0, 9);
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if ($subaction == "add_list_") {
				$clist=substr($action, 9);
				if (is_array($ids)) 
					foreach ($ids  as $id){ $nbrows += $this->process_addlist_action($id,$clist) ; }	
				else $nbrows += $this->process_addlist_action($ids,$clist) ;	
				}
			
		}		


		if (null!==$this->current_action()) {
			$action = $this->current_action() ;
			$subaction = substr($action, 0, 9);
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
			if ($subaction == "del_list_") {
				$clist=substr($action, 9);
				if (is_array($ids)) 
					foreach ($ids  as $id){ $nbrows += $this->process_dellist_action($id,$clist) ; }	
				else $nbrows += $this->process_dellist_action($ids,$clist) ;	
				}
			
		}		



		return $nbrows ;
	}


	function process_send_action($userid) {
		$user=get_userdata($userid) ;
		return VF_Auto_login::send_autologin($user->user_login);
	}
	function process_create_action($userid) {
		$user=get_userdata($userid);
		return VF_Auto_login::create_autologin($user->user_login) ;
	}

	function process_delete_action($userid) {
		$user=get_userdata($userid);
		return VF_Auto_login::delete_autologin($user->user_login) ;
	}

	function process_disable_action($userid) {
		$user=get_userdata($userid);
		return VF_Auto_login::disable_autologin($user->user_login) ;
	}
	function process_enable_action($userid) {
		$user=get_userdata($userid);
		return VF_Auto_login::enable_autologin($user->user_login) ;
	}
	function process_addgroup_action($userid,$groupid) {
		return VF_Tools::add_group_to_user($userid,$groupid) ;
	}
	function process_delgroup_action($userid,$groupid) {
		return VF_Tools::del_group_to_user($userid,$groupid) ;
	}
	function process_addlist_action($userid,$listid) {
		return VF_Tools::add_list_to_user($userid,$listid) ;
	}
	function process_dellist_action($userid,$listid) {
		return VF_Tools::del_list_to_user($userid,$listid) ;
	}
	


	/**
		 * [REQUIRED] This is the most important method
		 *
		 * It will get rows from database and prepare them to be showed in table
		 */
	function prepare_items(){

		$nbrows = 0 ;
		$group = (isset($_REQUEST['group']) && $_REQUEST['group'] >=0) ? $_REQUEST['group'] : false ;

		global $wpdb;
		$table_users = $wpdb->prefix . 'users'; // do not forget about tables prefix
		$table_groups = $wpdb->prefix .'uam_accessgroup_to_object'	;
		$table_autologins = $wpdb->prefix .'vf_auto_login'	;

		$per_page = 0+get_option('nbrows'); // constant, how much records will be shown per page

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		// here we configure table headers, defined in our methods
		$this->_column_headers = array($columns, $hidden, $sortable);

		// [OPTIONAL] process bulk action if any
		$nbrows = 	$this->process_bulk_action();

		// will be used in pagination settings
		if (!$group){
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_users");
		} else {
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_users um1  JOIN $table_groups um2 ON (um1.ID = um2.object_id AND um2.object_type = 'user') WHERE um2.group_id = $group");
		}
		//print "total : $total_items<br/>";
		// prepare query params, as usual current page, order by and order direction
		$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
		$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'user_nicename';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

		// [REQUIRED] define $items array
		// notice that last argument is ARRAY_A, so we will retrieve array
		if (!$group) {
			//	$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_users ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged*$per_page), ARRAY_A);
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT ID,user_nicename,user_email,sended_on,login_hash,status FROM $table_users LEFT OUTER JOIN $table_autologins ON user_nicename = username ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged*$per_page), ARRAY_A);
		} else {
			//	$this->items = $wpdb->get_results($wpdb->prepare("SELECT um1.ID, um1.user_nicename, um1.user_email,sended_on FROM wp_users um1 LEFT OUTER JOIN $table_autologins ON user_nicename = username JOIN wp_uam_accessgroup_to_object um2 ON ( um1.ID = um2.object_id AND um2.object_type =  'user' ) WHERE um2.group_id =%d LIMIT %d OFFSET %d ",$group,$per_page, $paged*$per_page), ARRAY_A);
				$this->items = $wpdb->get_results($wpdb->prepare("SELECT um1.ID, um1.user_nicename, um1.user_email,sended_on,login_hash,status FROM wp_users um1 LEFT OUTER JOIN $table_autologins ON user_nicename = username JOIN wp_uam_accessgroup_to_object um2 ON ( um1.ID = um2.object_id AND um2.object_type =  'user' ) WHERE um2.group_id =%d ORDER BY $orderby $order LIMIT %d OFFSET %d ",$group,$per_page, $paged*$per_page), ARRAY_A);
		}	
		foreach ($this->items as $key => $value) {
			//	$this->items[$key]['groups'] = VF_Users_List_Table::get_group(get_user_meta($value['ID'],'main_group',true));
			$this->items[$key]['groups'] = VF_Tools::get_groups_from_user($value['ID'],'<br />') ;
			$this->items[$key]['lists'] = VF_Tools::get_lists_from_user($value['ID'],'<br />');
		}
		// [REQUIRED] configure pagination
		$this->set_pagination_args(array(
				'total_items' => $total_items, // total items defined above
				'per_page' => $per_page, // per page constant defined at top of method
				'total_pages' => ceil($total_items / $per_page) // calculate pages count
		));
		return $nbrows ;
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable ) = $this->get_column_info();
	
		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( array('paged','id','action'), $current_url );
//		$current_url = remove_query_arg( 'paged', $current_url );
	
		if ( isset( $_GET['orderby'] ) )
			$current_orderby = $_GET['orderby'];
		else
			$current_orderby = '';
	
		if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
			$current_order = 'desc';
		else
			$current_order = 'asc';
	
		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}
	
		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );
	
			$style = '';
			if ( in_array( $column_key, $hidden ) )
				$style = 'display:none;';
	
			$style = ' style="' . $style . '"';
	
			if ( 'cb' == $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';
	
			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];
	
				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}
	
				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}
	
			$id = $with_id ? "id='$column_key'" : '';
	
			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";
	
			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}
	
	/**
	 * Generates the table navigation above or bellow the table and removes the
	 * _wp_http_referrer and _wpnonce because it generates a error about URL too large
	 * 
	 * @param string $which 
	 * @return void
	 */
	function display_tablenav( $which ) 
	{
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
	
			<div class="alignleft actions">
				<?php $this->bulk_actions(); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

}
?>
