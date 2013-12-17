<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if(!class_exists('Custom_tplus_Matches_Table')){
	class Custom_tplus_Matches_Table extends WP_List_Table
	{
		/**
		 * [REQUIRED] You must declare constructor and give some basic params
		 */
		function __construct(){
			global $status, $page,$wpdb;
                        $this->tablename = $wpdb->prefix."tplus_matches";

			parent::__construct(array(
				'singular' => 'incontro',
				'plural' => 'incontri',
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

		function column_placeid($item){
			global $wpdb;
			//retrieve place name from external table
			$sql = "SELECT plname FROM ".$wpdb->prefix."tplus_places WHERE id = ".$item['placeid'];
			$place = $wpdb->get_row($sql, ARRAY_A);
			return $place['plname'];
		}
		function column_tournamentid($item){
			global $wpdb;
			//retrieve place name from external table
			$sql = "SELECT tname FROM ".$wpdb->prefix."tplus_tournaments WHERE id = ".$item['tournamentid'];
			$tour = $wpdb->get_row($sql, ARRAY_A);
			return $tour['tname'];
		}

		/**
		 * [OPTIONAL] this is example, how to render column with actions,
		 * when you hover row "Edit | Delete" links showed
		 *
		 * @param $item - row (key, value array)
		 * @return HTML
		 */
		function column_playerid1($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on curren page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
			$actions = array(
				'edit' => sprintf('<a href="?page=tplus_matchesedit&id=%s">%s</a>', $item['id'], __('Edit', 'tplus_matches')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'tplus_matches')),
			);
                        $users = get_users('search='.$item['playerid1']);
                        $usernick = $users[0]->user_login;
			return sprintf('%s %s',
				$usernick,
				$this->row_actions($actions)
			);
		}
		function column_playerid2($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on curren page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
                        $users = get_users('search='.$item['playerid2']);
                        $usernick = $users[0]->user_login;
                        return $usernick;
		}
		function column_playerid3($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on curren page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
                        $users = get_users('search='.$item['playerid3']);
                        $usernick = $users[0]->user_login;
                        return $usernick;
		}
		function column_playerid4($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on curren page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
                        $users = get_users('search='.$item['playerid4']);
                        $usernick = $users[0]->user_login;
                        return $usernick;
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
		function get_columns()
		{
			$columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                                'id'        => 'id',
				'playerid1' => __('Giocatore 1', 'tplus_matches'),
				'playerid3' => __('Giocatore 3', 'tplus_matches'),
				'playerid2' => __('Giocatore 2', 'tplus_matches'),
				'playerid4' => __('Giocatore 4', 'tplus_matches'),
				'placeid' => __('Luogo', 'tplus_matches'),
				'matchdate' => __('Data incontro', 'tplus_matches'),
				'tournamentid' => __('Torneo', 'tplus_matches'),
				'pointstype' => __('Tipo Punteggio', 'tplus_matches'),
				'setspl1' => __('Set 1', 'tplus_matches'),
				'setspl2' => __('Set 2', 'tplus_matches'),
				'gamespl1' => __('Giochi 1', 'tplus_matches'),
				'gamespl2' => __('Giochi 2', 'tplus_matches'),
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
				'playerid1' => array('playerid1', true),
				'playerid2' => array('playerid2', true),
				'playerid3' => array('playerid3', true),
				'playerid4' => array('playerid4', true),
				'tournamentid' => array('tournamentid', false),
				'matchdate' => array('matchdate', false),
			);
			return $sortable_columns;
		}

		/**
		 * [OPTIONAL] Return array of bult actions if has any
		 *
		 * @return array
		 */
		function get_bulk_actions(){
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
		function prepare_items() {
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
			$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'placeid';
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
                function matches_form_page_handler(){
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'tplus_matches'; // do not forget about tables prefix
                        $message = '';
                        $notice = '';

                        // this is default $item which will be used for new records
                        $default = array(
                            'id' => 0,
                            'playerid1' => '',
                            'playerid2' => '',
                            'playerid3' => '',
                            'playerid4' => '',
                            'placeid'=>1,
                            'matchdate' => date("Y-m-d H:i:s"),
                            'tournamentid'=>'',
                            'pointstype' => '',
                            'setspl1' => '',
                            'setspl2' => '',
                            'gamespl1' => '',
                            'gamespl2' => '',
                        );

                        // here we are verifying does this request is post back and have correct nonce
                        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
                                // combine our default item with request params
                                $item = shortcode_atts($default, $_REQUEST);
                                // validate data, and if all ok save item to database
                                // if id is zero insert otherwise update
                                $item_valid = self::matches_validate($item);
                                if ($item_valid === true) {
                                        if ($item['id'] == 0) {
                                                $result = $wpdb->insert($table_name, $item);
                                                $item['id'] = $wpdb->insert_id;
                                                if ($result) {
                                                        $message = __('Item was successfully saved', 'tplus_matches');
                                                } else {
                                                        $notice = __('There was an error while saving item', 'tplus_matches');
                                                }
                                        } 
                                        else {
                                                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                                                if ($result) {
                                                        $message = __('Item was successfully updated', 'tplus_matches');
                                                } 
                                                else {
                                                        $notice = __('There was an error while updating item'.$wpdb->last_error, 'tplus_matches');
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
                                                $notice = __('Item not found', 'tplus_matches');
                                        }
                                }
                        }

                        // here we adding our custom meta box
                        add_meta_box('matches_form_meta_box', 'Matches data', array('Custom_tplus_Matches_Table','matches_form_meta_box_handler'), 'match', 'normal', 'default');
                        ?>
                        <div class="wrap">
                        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2><?php _e('Incontri', 'tplus_matches')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_matches');?>"><?php _e('elenco incontri', 'tplus_matches')?></a>
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
                                                <?php do_meta_boxes('match', 'normal', $item); ?>
                                                <input type="submit" value="<?php _e('Salva', 'tplus_matches')?>" id="submit" class="button-primary" name="submit">
                                                <h3><?php _e('NOTA: In caso di incontro singolo non selezionare giocatori 3 e 4', 'tplus_matches')?></h3>
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
                function matches_form_meta_box_handler($item){
                        global $wpdb;
                        $tours = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'tplus_tournaments', 100, 0), ARRAY_A);
                        $places = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'tplus_places', 100, 0), ARRAY_A);
                        $points = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'tplus_points', 100, 0), ARRAY_A);
                        $players = get_users();
                ?>
                <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                    <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="playerid1"><?php _e('Incontro', 'tplus_matches')?></label>
                        </th>
                        <td>
                                <select id="playerid1" name="playerid1">
                                        <option value="0">Scegli giocatore 1</option>
                                        <?php
                                        foreach($players as $v){
                                                echo "<option value='".$v->ID."'".($item['playerid1']==$v->ID?" selected='selected'":'').">".$v->user_nicename."</option>";
                                        }
                                        ?>
                                </select>
                                <select id="playerid3" name="playerid3">
                                        <option value="0">Scegli giocatore 3</option>
                                        <?php
                                        foreach($players as $v){
                                                echo "<option value='".$v->ID."'".($item['playerid3']==$v->ID?" selected='selected'":'').">".$v->user_nicename."</option>";
                                        }
                                        ?>
                                </select>
                                &nbsp;&nbsp;&nbsp;VS.&nbsp;&nbsp;&nbsp;
                                <select id="playerid2" name="playerid2">
                                        <option value="0">Scegli giocatore 2</option>
                                        <?php
                                        foreach($players as $v){
                                                echo "<option value='".$v->ID."'".($item['playerid2']==$v->ID?" selected='selected'":'').">".$v->user_nicename."</option>";
                                        }
                                        ?>
                                </select>
                                <select id="playerid4" name="playerid4">
                                        <option value="0">Scegli giocatore 4</option>
                                        <?php
                                        foreach($players as $v){
                                                echo "<option value='".$v->ID."'".($item['playerid4']==$v->ID?" selected='selected'":'').">".$v->user_nicename."</option>";
                                        }
                                        ?>
                                </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="placeid"><?php _e('Luogo', 'tplus_matches')?></label>
                        </th>
                        <td>
                                <select id="placeid" name="placeid">
                                        <option value="0">Scegli Luogo</option>
                                        <?php
                                        foreach($places as $v){
                                                echo "<option value='".$v['id']."'".($item['placeid']==$v['id']?" selected='selected'":'').">".$v['plname']."</option>";
                                        }
                                        ?>
                                </select>
                                &nbsp;&nbsp;<?php _e('Data', 'tplus_matches')?>: &nbsp;&nbsp;
                                <input id="matchdate" name="matchdate" style="width: 35%" value="<?php echo esc_attr($item['matchdate'])?>"
                                   size="50" class="code" placeholder="<?php _e('Data incontro', 'tplus_matches')?>" required>
                                &nbsp;&nbsp;<?php _e('Torneo', 'tplus_matches')?>: &nbsp;&nbsp;
                                <select id="tournamentid" name="tournamentid">
                                        <option value="0">Scegli torneo</option>
                                        <?php
                                        foreach($tours as $v){
                                                echo "<option value='".$v['id']."'".($item['tournamentid']==$v['id']?" selected='selected'":'').">".$v['tname']."</option>";
                                        }
                                        ?>
                                </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="pointstype"><?php _e('Punteggio', 'tplus_tournaments')?></label>
                        </th>
                        <td>
                                <select id="pointstype" name="pointstype">
                                        <option vlaue="0"><?php _e('Punteggio', 'tplus_tournaments')?></option>
                                        <?php
                                        foreach($points as $v){
                                                echo "<option value='".$v['id']."'".($item['pointstype']==$v['id']?" selected='selected'":'').">".$v['plabel']."</option>";
                                        }
                                        ?>
                                </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="setspl1"><?php _e('Sets', 'tplus_matches')?></label>
                        </th>
                        <td>
                            <input id="setspl1" name="setspl1" type="text" style="width: 25%" value="<?php echo esc_attr($item['setspl1'])?>"
                                   size="50" class="code" placeholder="<?php _e('Set 1', 'tplus_matches')?>">
                            &nbsp;&nbsp;&nbsp;VS.&nbsp;&nbsp;&nbsp;
                            <input id="setspl2" name="setspl2" type="text" style="width: 25%" value="<?php echo esc_attr($item['setspl2'])?>"
                                   size="50" class="code" placeholder="<?php _e('Set 2', 'tplus_matches')?>">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="gamespl1"><?php _e('Giochi', 'tplus_matches')?></label>
                        </th>
                        <td>
                            <input id="gamespl1" name="gamespl1" type="text" style="width: 25%" value="<?php echo esc_attr($item['gamespl1'])?>"
                                   size="50" class="code" placeholder="<?php _e('Giochi 1', 'tplus_matches')?>">
                        &nbsp;&nbsp;&nbsp;VS.&nbsp;&nbsp;&nbsp;
                            <input id="gamespl2" name="gamespl2" type="text" style="width: 25%" value="<?php echo esc_attr($item['gamespl2'])?>"
                                   size="50" class="code" placeholder="<?php _e('Giochi 2', 'tplus_matches')?>">
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
                function matches_validate($item){
                    $messages = array();

                    if (empty($item['playerid1'])) $messages[] = __('Giocatore 1 obbligatorio', 'tplus_matches');
                    if (empty($item['playerid2'])) $messages[] = __('Giocatore 2 obbligatorio', 'tplus_matches');
                    if (empty($item['matchdate'])) $messages[] = __('Data incontro obbligatoria', 'tplus_matches');
                    if (empty($item['pointstype'])) $messages[] = __('Tipologia punteggio obbligatoria', 'tplus_matches');
                    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
                    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
                    //...

                    if (empty($messages)) return true;
                    return implode('<br />', $messages);
                }
	}
}