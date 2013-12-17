<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if(!class_exists('Custom_tplus_Places_Table')){
	class Custom_tplus_Places_Table extends WP_List_Table{
		/**
		 * [REQUIRED] You must declare constructor and give some basic params
		 */
		function __construct(){
			global $status, $page,$wpdb;
                        $this->tablename = $wpdb->prefix."tplus_places";
			parent::__construct(array(
				'singular' => 'luogo',
				'plural' => 'luoghi',
                                'ajax'	=> false,
			));
		}
                //this code is required for table header and footer
                /*function extra_tablenav( $which ) {
                        if ( $which == "top" ){
                                //The code that goes before the table is here
                                echo"Hello, I'm before the table";
                        }
                        if ( $which == "bottom" ){
                                //The code that goes after the table is there
                                echo"Hi, I'm after the table";
                        }       
                }*/
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
		
		function column_date_start($item){
			return date("d/m/Y - H:i",$item['date_start']);
		}
		function column_date_end($item){
			return date("d/m/Y - H:i",$item['date_start']);
		}
		/**
		 * [OPTIONAL] this is example, how to render column with actions,
		 * when you hover row "Edit | Delete" links showed
		 *
		 * @param $item - row (key, value array)
		 * @return HTML
		 */
		function column_plname($item)	{
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on current page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
			$actions = array(
				'edit' => sprintf('<a href="?page=tplus_placesedit&id=%s">%s</a>', $item['id'], __('Edit', 'tplus_places')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'tplus_places')),
			);

			return sprintf('%s %s',
				$item['plname'],
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
                                'plname' => __('Nome', 'tplus_places'),
				//'pldescription' => __('Descrizione', 'tplus_places'),
				'plcity' => __('Citta', 'tplus_places'),
				'plprovince' => __('Provincia', 'tplus_places'),
				'pladdress' => __('Indirizzo', 'tplus_places'),
				'plphone' => __('Telefono', 'tplus_places'),
				'plmobile' => __('Cellulare', 'tplus_places'),
				'plmail' => __('Email', 'tplus_places'),
				'plrefperson' => __('Referente', 'tplus_places'),
				'plfield1' => __('Campo 1', 'tplus_places'),
				'plfield2' => __('Campo 2', 'tplus_places'),
				'plfield3' => __('Campo 3', 'tplus_places'),
				'plfield4' => __('Campo 4', 'tplus_places'),
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
				'plname' => array('plname', true),
				'plcity' => array('plcity', false),
				'plprovince' => array('plprovince', false),
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

			// here we configure table headers, defined in our methods
			$this->_column_headers = array($columns, $hidden, $sortable);

			// [OPTIONAL] process bulk action if any
			$this->process_bulk_action();

			// will be used in pagination settings
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$this->tablename);

			// prepare query params, as usual current page, order by and order direction
			$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
			$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'plname';
			$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$this->tablename." ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

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
                function places_form_page_handler(){
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'tplus_places'; // do not forget about tables prefix
                        $message = '';
                        $notice = '';

                        // this is default $item which will be used for new records
                        $default = array(
                                'id' => 0,
                                'plname' => '',
                                'pldescription' => '',
                                'lat'=>'',
                                'lon'=>'',
                                'plcity'=>'',
                                'plprovince'=>'',
                                'pladdress'=>'',
                                'plphone'=>'',
                                'plmail'=>'',
                                'plmobile'=>'',
                                'plrefperson'=>'',
                                'plfield1'=>'',
                                'plfield2'=>'',
                                'plfield3'=>'',
                                'plfield4'=>'',
                        );

                        // here we are verifying does this request is post back and have correct nonce
                        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
                                // combine our default item with request params
                                $item = shortcode_atts($default, $_REQUEST);
                                // validate data, and if all ok save item to database
                                // if id is zero insert otherwise update
                                $item_valid = self::places_validate($item);
                                if ($item_valid === true) {
                                        if ($item['id'] == 0) {
                                                $result = $wpdb->insert($table_name, $item);
                                                $item['id'] = $wpdb->insert_id;
                                                if ($result) {
                                                        $message = __('Item was successfully saved', 'tplus_places');
                                                } else {
                                                        $notice = __('There was an error while saving item', 'tplus_places');
                                                }
                                        } 
                                        else {
                                                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                                                if ($result) {
                                                        $message = __('Item was successfully updated', 'tplus_places');
                                                } 
                                                else {
                                                        $notice = __('There was an error while updating item'.$wpdb->last_error, 'tplus_places');
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
                                                $notice = __('Item not found', 'tplus_places');
                                        }
                                }
                        }

                        // here we adding our custom meta box
                        add_meta_box('places_form_meta_box', 'Luoghi', array('Custom_tplus_Places_Table','places_form_meta_box_handler'), 'place', 'normal', 'default');
                        ?>
                        <div class="wrap">
                        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2><?php _e('Tornei', 'tplus_places')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_places');?>"><?php _e('back to list', 'tplus_places')?></a>
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
                                                <?php do_meta_boxes('place', 'normal', $item); ?>
                                                <input type="submit" value="<?php _e('Save', 'tplus_places')?>" id="submit" class="button-primary" name="submit">
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
                function places_form_meta_box_handler($item){
                        global $wpdb;
                        $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'tplus_places', 100, 0), ARRAY_A);
                ?>
                <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                    <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plname"><?php _e('Nome luogo', 'tplus_places')?></label>
                        </th>
                        <td colspan="4">
                            <input id="plname" name="plname" type="text" style="width: 95%" value="<?php echo esc_attr($item['plname'])?>"
                                   size="50" class="code" placeholder="<?php _e('Nome luogo', 'tplus_places')?>" required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pldescription"><?php _e('Descrizione luogo', 'tplus_places')?></label>
                        </th>
                        <td colspan="4">
                                <?php 
                                $settings = array( 'media_buttons' => false );
                                wp_editor( $item['pldescription'], 'pldescription',$settings );
                                ?>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pladdress"><?php _e('Indirizzo', 'tplus_places')?></label>
                        </th>
                        <td colspan="4">
                                <input id="date_start" name="pladdress" style="width: 95%" value="<?php echo esc_attr($item['pladdress'])?>"
                                   size="50" class="code" placeholder="<?php _e('Indirizzo', 'tplus_places')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plcity"><?php _e('Citta', 'tplus_places')?></label>
                        </th>
                        <td colspan="4">
                                <input id="plcity" name="plcity" style="width: 95%" value="<?php echo esc_attr($item['plcity'])?>"
                                   size="50" class="code" placeholder="<?php _e('Citta', 'tplus_places')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plprovince"><?php _e('Provincia', 'tplus_places')?></label>
                        </th>
                        <td colspan="4">
                                <input id="plprovince" name="plprovince" style="width: 95%" value="<?php echo esc_attr($item['plprovince'])?>"
                                   size="50" class="code" placeholder="<?php _e('Provincia', 'tplus_places')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plphone"><?php _e('Contatti', 'tplus_places')?></label>
                        </th>
                        <td colspan="2">
                                <input id="plphone" name="plphone" style="width: 95%" value="<?php echo esc_attr($item['plphone'])?>"
                                   size="50" class="code" placeholder="<?php _e('Telefono', 'tplus_places')?>">
                        </td>
                        <td>
                                <input id="plmobile" name="plmobile" style="width: 95%" value="<?php echo esc_attr($item['plmobile'])?>"
                                   size="50" class="code" placeholder="<?php _e('Cellulare', 'tplus_places')?>">
                        </td>
                        <td>
                                <input id="plmail" name="plmail" style="width: 95%" value="<?php echo esc_attr($item['plmail'])?>"
                                   size="50" class="code" placeholder="<?php _e('Email', 'tplus_places')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="plfield1"><?php _e('Campi', 'tplus_places')?></label>
                        </th>
                        <td>
                            <input id="plfield1" name="plfield1" type="text" style="width: 95%" value="<?php echo esc_attr($item['plfield1'])?>"
                                   size="50" class="code" placeholder="<?php _e('Campo 1', 'tplus_places')?>">
                        </td>
                        <td>
                            <input id="plfield2" name="plfield2" type="text" style="width: 95%" value="<?php echo esc_attr($item['plfield2'])?>"
                                   size="50" class="code" placeholder="<?php _e('Campo 2', 'tplus_places')?>">
                        </td>
                        <td>
                            <input id="plfield3" name="plfield3" type="text" style="width: 95%" value="<?php echo esc_attr($item['plfield3'])?>"
                                   size="50" class="code" placeholder="<?php _e('Campo 3', 'tplus_places')?>">
                        </td>
                        <td>
                            <input id="plfield4" name="plfield4" type="text" style="width: 95%" value="<?php echo esc_attr($item['plfield4'])?>"
                                   size="50" class="code" placeholder="<?php _e('Campo 4', 'tplus_places')?>">
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
                function places_validate($item){
                    $messages = array();

                    if (empty($item['plname'])) $messages[] = __('Nome luogo obbligatorio', 'tplus_places');
                    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
                    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
                    //...

                    if (empty($messages)) return true;
                    return implode('<br />', $messages);
                }
	}
}