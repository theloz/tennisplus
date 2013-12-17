<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if(!class_exists('Custom_tplus_Points_Table')){
	class Custom_tplus_Points_Table extends WP_List_Table{
		/**
		 * [REQUIRED] You must declare constructor and give some basic params
		 */
		function __construct(){
			global $status, $page,$wpdb;
                        $this->tablename = $wpdb->prefix."tplus_points";
			parent::__construct(array(
				'singular' => 'punto',
				'plural' => 'punti',
                                'ajax'	=> false,
			));
		}
                //this code is required for table header and footer
                function extra_tablenav( $which ) {
                        /*global $wpdb;
                        if ( $which == "top" ){
                                //The code that goes before the table is here
                                $place = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'tplus_places', 100, 0), ARRAY_A);
                                ?>
                                <div style="float:right">
                                <?php echo __('Ricerca', 'tplus_points').": ";?>
                                <form method="post">
                                        <input type="text" name="s[tour]" placeholder="<?php echo __('Torneo', 'tplus_points')?>" />
                                        <select id="placeid" name="s[placeid]">
                                                <option value="">Luogo</option>
                                                <?php
                                                foreach($place as $v){
                                                        echo "<option value='".$v['id']."'>".$v['plname']."</option>";
                                                }
                                                ?>
                                        </select>
                                        <button type="submit"><?php echo __('Cerca', 'tplus_points')?></button>
                                </form>
                                </div>
                                <?php
                        }
                        if ( $which == "bottom" ){
                                //The code that goes after the table is there
                                echo"Hi, I'm after the table";
                        }*/
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
		 * [OPTIONAL] this is example, how to render column with actions,
		 * when you hover row "Edit | Delete" links showed
		 *
		 * @param $item - row (key, value array)
		 * @return HTML
		 */
		function column_tlabel($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on current page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
			$actions = array(
				'edit' => sprintf('<a href="?page=tplus_pointsedit&id=%s">%s</a>', $item['id'], __('Edit', 'tplus_points')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'tplus_points')),
			);

			return sprintf('%s %s',
				$item['tname'],
				$this->row_actions($actions)
			);
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
				$item['id']
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
				'id'=>'id',
                                'pdesc' => __('Descrizione', 'tplus_points'),
				'plabel' => __('Nome', 'tplus_points'),
				'pfullwin' => __('Vittoria assoluta', 'tplus_points'),
				'pwin' => __('Vittoria parziale', 'tplus_points'),
				'pdraw' => __('Pareggio', 'tplus_points'),
				'plose' => __('Sconfitta', 'tplus_points'),
				'pfulllose' => __('Sconfitta assoluta', 'tplus_points'),
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
				'plabel' => array('plabel', true),
				'pfullwin' => array('pfullwin', false),
				'pwin' => array('pwin', false),
				'pdraw' => array('pdraw', false),
				'plose' => array('plose', false),
				'pfulllose' => array('plose', false),
			);
			return $sortable_columns;
		}

		/**
		 * [OPTIONAL] Return array of bult actions if has any
		 *
		 * @return array
		 */
		function get_bulk_actions()	{
			$actions = array(
				'delete' => 'Delete'
			);
			return $actions;
		}

		/**
		 * [REQUIRED] This is the most important method
		 *
		 * It will get rows from database and prepare them to be showed in table
		 */
		function prepare_items(){
			global $wpdb;
			$per_page = 10; // constant, how much records will be shown per page

			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
                        //search logic
                        $qstr = '';
                        /*if(isset($_REQUEST['s'])){
                                $placekey = $_REQUEST['s']['placeid'];
                                $tourkey = $_REQUEST['s']['tour'];
                                //if any of the terms has been selected, i'll compose where condition
                                if( $placekey != '' || $tourkey != "" ){
                                        $qstr = ' WHERE ';
                                        if($placekey!=''){
                                                $qstr .= 'placeid = '.$placekey.' AND ';
                                        }
                                        if($tourkey!=''){
                                                $qstr .= "tname LIKE '%%$tourkey%%' AND "; //doubles the % to work with wpdb->prepare
                                        }
                                        $qstr = substr($qstr, 0, -5);
                                }
                        }*/
                        
			// here we configure table headers, defined in our methods
			$this->_column_headers = array($columns, $hidden, $sortable);

			// [OPTIONAL] process bulk action if any
			$this->process_bulk_action();

			// will be used in pagination settings
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$this->tablename.$qstr);

			// prepare query params, as usual current page, order by and order direction
			$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
			$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'plabel';
			$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
                        //die("SELECT * FROM ".$this->tablename."$qstr ORDER BY $orderby $order LIMIT $per_page OFFSET $paged");
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$this->tablename."$qstr ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

			// [REQUIRED] configure pagination
			$this->set_pagination_args(array(
				'total_items' => $total_items, // total items defined above
				'per_page' => $per_page, // per page constant defined at top of method
				'total_pages' => ceil($total_items / $per_page) // calculate pages count
			));
		}
                /**
		 * [OPTIONAL] This method processes bulk actions
		 * it can be outside of class
		 * it can not use wp_redirect coz there is output already
		 * in this example we are processing delete action
		 * message about successful deletion will be shown on page in next part
		 */
		function process_bulk_action(){
			global $wpdb;

			if ('delete' === $this->current_action()) {
				$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
				if (is_array($ids)) $ids = implode(',', $ids);

				if (!empty($ids)) {
					$wpdb->query("DELETE FROM ".$this->tablename." WHERE id IN($ids)");
				}
			}
		}
                function points_form_page_handler(){
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'tplus_points'; // do not forget about tables prefix
                        $message = '';
                        $notice = '';

                        // this is default $item which will be used for new records
                        $default = array(
                                'id' => 0,
                                'pdesc' => '',
				'plabel' => '',
				'pfullwin' => 0,
				'pwin' => 0,
				'pdraw' => 0,
				'plose' => 0,
				'pfulllose' => 0,
                        );

                        // here we are verifying does this request is post back and have correct nonce
                        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
                                // combine our default item with request params
                                $item = shortcode_atts($default, $_REQUEST);
                                // validate data, and if all ok save item to database
                                // if id is zero insert otherwise update
                                $item_valid = self::points_validate($item);
                                if ($item_valid === true) {
                                        if ($item['id'] == 0) {
                                                $result = $wpdb->insert($table_name, $item);
                                                $item['id'] = $wpdb->insert_id;
                                                if ($result) {
                                                        $message = __('Item was successfully saved', 'tplus_points');
                                                } else {
                                                        $notice = __('There was an error while saving item', 'tplus_points');
                                                }
                                        } 
                                        else {
                                                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                                                if ($result) {
                                                        $message = __('Item was successfully updated', 'tplus_points');
                                                } 
                                                else {
                                                        $notice = __('There was an error while updating item'.$wpdb->last_error, 'tplus_points');
                                                }
                                        }
                                } 
                                else {
                                        // if $item_valid not true it contains error message(s)
                                        $notice = $item_valid;
                                }
                        }
                        else {
                                // if this is not post back we load item to edit or give new one to create
                                $item = $default;
                                if (isset($_REQUEST['id'])) {
                                        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$table_name." WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                                        if (!$item) {
                                                $item = $default;
                                                $notice = __('Item not found', 'tplus_points');
                                        }
                                }
                        }

                        // here we adding our custom meta box
                        add_meta_box('points_form_meta_box', 'Point data', array('Custom_tplus_Points_Table','points_form_meta_box_handler'), 'point', 'normal', 'default');
                        ?>
                        <div class="wrap">
                        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2><?php _e('Punteggi', 'tplus_points')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_points');?>"><?php _e('back to list', 'tplus_points')?></a>
                        </h2>

                        <?php if (!empty($notice)): ?>
                        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
                        <?php endif;?>
                        <?php if (!empty($message)): ?>
                        <div id="message" class="updated"><p><?php echo $message ?></p></div>
                        <?php endif;?>

                                <form id="form" method="POST">
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                                    <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                                    <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

                                    <div class="metabox-holder" id="poststuff">
                                        <div id="post-body">
                                            <div id="post-body-content">
                                                <?php /* And here we call our custom meta box */ ?>
                                                <?php do_meta_boxes('point', 'normal', $item); ?>
                                                <input type="submit" value="<?php _e('Save', 'tplus_points')?>" id="submit" class="button-primary" name="submit">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                        </div>
                        <?php
                }

                /**
                 * This function renders our custom meta box
                 * $item is row
                 *
                 * @param $item
                 */
                function points_form_meta_box_handler($item){
                ?>
                <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                    <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pdesc"><?php _e('Descrizione', 'tplus_points')?></label>
                        </th>
                        <td>
                            <input id="pdesc" name="pdesc" type="text" style="width: 95%" value="<?php echo esc_attr($item['pdesc'])?>"
                                   size="255" class="code" placeholder="<?php _e('Descrizione', 'tplus_points')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plabel"><?php _e('Etichetta punteggio', 'tplus_points')?></label>
                        </th>
                        <td>
                            <input id="plabel" name="plabel" type="text" style="width: 95%" value="<?php echo esc_attr($item['plabel'])?>"
                                   size="20" class="code" placeholder="<?php _e('Etichetta', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pfullwin"><?php _e('Vittoria assoluta', 'tplus_points')?></label>
                        </th>
                        <td>
                                 <input id="pfullwin" name="pfullwin" type="text" style="width: 95%" value="<?php echo esc_attr($item['pfullwin'])?>"
                                   size="2" class="code" placeholder="<?php _e('Vittoria assoluta', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pwin"><?php _e('Vittoria parziale', 'tplus_points')?></label>
                        </th>
                        <td>
                                 <input id="pwin" name="pwin" type="text" style="width: 95%" value="<?php echo esc_attr($item['pwin'])?>"
                                   size="2" class="code" placeholder="<?php _e('Vittoria parziale', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pdraw"><?php _e('Pareggio', 'tplus_points')?></label>
                        </th>
                        <td>
                                 <input id="pdraw" name="pdraw" type="text" style="width: 95%" value="<?php echo esc_attr($item['pdraw'])?>"
                                   size="2" class="code" placeholder="<?php _e('Pareggio', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plose"><?php _e('Sconfitta', 'tplus_points')?></label>
                        </th>
                        <td>
                                 <input id="plose" name="plose" type="text" style="width: 95%" value="<?php echo esc_attr($item['plose'])?>"
                                   size="2" class="code" placeholder="<?php _e('Sconfitta', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pfulllose"><?php _e('Sconfitta assoluta', 'tplus_points')?></label>
                        </th>
                        <td>
                                 <input id="pfulllose" name="pfulllose" type="text" style="width: 95%" value="<?php echo esc_attr($item['pfulllose'])?>"
                                   size="2" class="code" placeholder="<?php _e('Sconfitta assoluta', 'tplus_points')?>" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
                }

                /**
                 * Simple function that validates data and retrieve bool on success
                 * and error message(s) on error
                 *
                 * @param $item
                 * @return bool|string
                 */
                function points_validate($item){
                    $messages = array();

                    if (empty($item['plabel'])) $messages[] = __('Etichetta obbligatoria', 'tplus_points');
                    if (empty($item['pfullwin'])) $messages[] = __('Vittoria assoluta obbligatoria', 'tplus_points');
                    if(!empty($item['pfullwin']) && !is_int((int)$item['pfullwin'])) $messages[] = __('Vittoria assoluta deve essere un numero', 'tplus_points');
                    if(!empty($item['pwin']) && !is_int((int)$item['pwin'])) $messages[] = __('Vittoria deve essere un numero', 'tplus_points');
                    if (empty($item['pdraw'])) $messages[] = __('Pareggio obbligatorio', 'tplus_points');
                    if(!empty($item['pdraw']) && !is_int((int)$item['pdraw'])) $messages[] = __('Pareggio deve essere un numero', 'tplus_points');
                    if(!empty($item['plose']) && !is_int((int)$item['plose'])) $messages[] = __('Sconfitta deve essere un numero', 'tplus_points');
                    if(!empty($item['pfulllose']) && !is_int((int)$item['pfulllose'])) $messages[] = __('Sconfitta assoluta deve essere un numero', 'tplus_points');
                    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
                    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
                    //...

                    if (empty($messages)) return true;
                    return implode('<br />', $messages);
                }
	}
}