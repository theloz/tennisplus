<?php
if(!class_exists('Admin_tplus')){
	class Admin_tplus{
		public function get_menu(){
				add_menu_page(
					__('Tennis Plus', 'tplus_db'), 
					__('Tennis Plus', 'tplus_db'), 
					'activate_plugins', 
					'tennisplus', 
					array('Admin_tplus','menu_welcome'),
                                        plugins_url( 'img/ico_small.png' , dirname(__FILE__) )
				);
				add_submenu_page(
					'tennisplus', 
					__('Luogo', 'tplus_db'), 
					__('Luogo', 'tplus_db'), 
					'activate_plugins', 
					'tplus_places', 
					array('Admin_tplus','places')
				);
				add_submenu_page(
                                        null, //exclude from sidebar menu
					__('Aggiungi luogo', 'tplus_db'), 
					__('Aggiungi luogo', 'tplus_db'), 
					'activate_plugins', 
					'tplus_placesedit', 
					array('Custom_tplus_Places_Table','places_form_page_handler')
				);
				// add new will be described in next part
				add_submenu_page(
					'tennisplus', 
					__('Tornei', 'tplus_db'), 
					__('Tornei', 'tplus_db'), 
					'activate_plugins', 
					'tplus_tournaments', 
					array('Admin_tplus','tournaments')
				);
				add_submenu_page(
					null, 
					__('Aggiungi torneo', 'tplus_db'), 
					__('Aggiungi torneo', 'tplus_db'), 
					'activate_plugins', 
					'tplus_tournamentsedit',
					array('Custom_tplus_Tournament_Table','tournaments_form_page_handler')
				);
				add_submenu_page(
					'tennisplus', 
					__('Incontri', 'tplus_db'), 
					__('Incontri', 'tplus_db'), 
					'activate_plugins', 
					'tplus_matches', 
					array('Admin_tplus','matches')
				);
                                add_submenu_page(
					null, 
					__('Aggiungi incontro', 'tplus_db'), 
					__('Aggiungi incontro', 'tplus_db'), 
					'activate_plugins', 
					'tplus_matchesedit',
					array('Custom_tplus_Matches_Table','matches_form_page_handler')
				);
				add_submenu_page(
					'tennisplus', 
					__('Punti', 'tplus_db'), 
					__('Punti', 'tplus_db'), 
					'activate_plugins', 
					'tplus_points', 
					array('Admin_tplus','points')
				);
                                add_submenu_page(
					null, 
					__('Aggiungi punteggio', 'tplus_db'), 
					__('Aggiungi punteggio', 'tplus_db'), 
					'activate_plugins', 
					'tplus_pointsedit',
					array('Custom_tplus_Points_Table','points_form_page_handler')
				);
				add_submenu_page(
					'tennisplus', 
					__('Iscrizioni', 'tplus_db'), 
					__('Iscrizioni', 'tplus_db'), 
					'activate_plugins', 
					'tplus_subs', 
					array('Admin_tplus','subs')
				);
                                add_submenu_page(
					null, 
					__('Aggiungi iscrizione', 'tplus_db'), 
					__('Aggiungi iscrizione', 'tplus_db'), 
					'activate_plugins', 
					'tplus_subsedit',
					array('Custom_tplus_Subs_Table','subs_form_page_handler')
				);
		}
		public function menu_welcome(){
                        $msg = '';
                        if(isset($_POST['admin'])){
                                if($_POST['admin']['dbbkp']=='ok'){
                                        $tb = new WP_tplus_tables;
                                        $fname = $tb->tables_dump();
                                }
                                if($_POST['admin']['dbreset']=='ok' && $_POST['admin']['dbresetconfirm']=='ok'){
                                        $tb = new WP_tplus_tables;
                                        $fname = $tb->table_reset();
                                }
                                elseif($_POST['admin']['dbreset']=='ok'){
                                        $msg .= '<h3 class="tplus_msg_error">'.__('Devi scegliere conferma', 'tplus_admin').'</h3>';
                                }
                                if($_POST['admin']['fupload']=='ok'){
                                        $sel = $_POST['admin']['fileselect'];
                                        do{
                                                if($sel == '0'){
                                                        $msg .= "<h3 class='tplus_msg_error'>".__('Selezionare il tipo di file','tplus_admin')."" . $_FILES["datafile"]["error"] . "</h3>";
                                                        break; 
                                                }
                                                if ($_FILES["datafile"]["error"] > 0){
                                                        $msg .= "<h3 class='tplus_msg_error'>File upload error: " . $_FILES["datafile"]["error"] . "</h3>";
                                                        break;
                                                }
                                                if ( !in_array( $_FILES["datafile"]["type"], array("text/csv","application/vnd.ms-excel") ) ){
                                                        $msg .= "<h3 class='tplus_msg_error'>".__('Tipo file non riconosciuto','tplus_admin').": " . $_FILES["datafile"]["type"] . "</h3>";
                                                        break;
                                                }
                                                if (($handle = fopen($_FILES["datafile"]["tmp_name"], "r")) !== FALSE) {
                                                        $row = 0;
                                                        $dataput = '';
                                                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                                                $dataput .= "('".implode("','",$data)."'),";
                                                                //$num = count($data);
                                                                //echo "<p> $num fields in line $row: <br /></p>\n";
                                                                $row++;
                                                                /*for ($c=0; $c < $num; $c++) {
                                                                    echo $data[$c] . "<br />\n";
                                                                }*/
                                                        }
                                                        $dataput = substr($dataput, 0, -1);
                                                        fclose($handle);
                                                        global $wpdb;
                                                        switch( $sel ){
                                                                case 'points':
                                                                        $table = $wpdb->prefix."tplus_points";
                                                                        $q = "INSERT INTO $table (id, `pdesc`, `plabel`, `pfullwin`, `pwin`, `pdraw`, `plose`, `pfulllose`) VALUES ";
                                                                        break;
                                                                case 'matches':
                                                                        $table = $wpdb->prefix."tplus_matches";
                                                                        $q = "INSERT INTO $table (id, `playerid1`, `playerid2`, `playerid3`, `playerid4`, `placeid`, `matchdate`, `tournamentid`, `pointstype`, `setspl1`, `setspl2`, `gamespl1`, `gamespl2`) VALUES ";
                                                                        break;
                                                                case 'tours':
                                                                        $table = $wpdb->prefix."tplus_tournaments";
                                                                        $q = "INSERT INTO $table (id, `tname`, `tdesc`, `pointstype`, `placeid`, `date_start`, `date_end`) VALUES ";
                                                                        break;
                                                                case 'places':
                                                                        $table = $wpdb->prefix."tplus_places";
                                                                        $q = "INSERT INTO $table (id, `plname`, `pldescription`, `lat`, `lon`, `plcity`, `plprovince`, `pladdress`, `plphone`, `plmail`, `plmobile`, `plrefperson`, `plfield1`, `plfield2`, `plfield3`, `plfield4`, `plnote`) VALUES ";
                                                                        break;
                                                                case 'subs':
                                                                        $table = $wpdb->prefix."tplus_subscriptions";
                                                                        $q = "INSERT INTO $table (id, `fk_userid`, `fk_tour`, `pending`) VALUES ";
                                                                        break;
                                                        }
                                                        if($_POST['admin']['fileoperation']==1){
                                                                $wpdb->query("TRUNCATE TABLE ".$table);
                                                        }
                                                        $sql = $q.$dataput;
                                                        if($wpdb->query($sql)){
                                                                $msg .= "<h3 class='tplus_msg_success'>".__('Caricamento dati effettuato','tplus_admin')."</h3>";
                                                                break;
                                                        }
                                                        else{
                                                                $msg .= "<h3 class='tplus_msg_error'>".__('Errore caricamento dati','tplus_admin')."</h3>";
                                                                break;
                                                        }
                                                }
                                                else{
                                                        $msg .= "<h3 class='tplus_msg_error'>".__('Impossibile leggere il file','tplus_admin')."</h3>";
                                                        break; 
                                                }
                                        }while(0);
                                }
                        }
                        $updir = wp_upload_dir();
                        $bkpfiles = @scandir($updir['basedir'].'/tplusbackups/',1);
                        echo "<div class='wrap'>";
                        echo "<p><img src='".plugins_url( 'img/ico.png' , dirname(__FILE__) )."' align='left' /> <br /><h1>Tennis plus admin</h1></p><br /><br />";
                        echo $msg;
                        ?>
                        <h3>Gestione dati</h3>
                        <form method="post" action="<?php echo admin_url(); ?>admin.php?page=tennisplus" enctype="multipart/form-data">
                                <label for="datafile" ><?php _e('File da caricare', 'tplus_admin') ?></label>
                                <input type="file" name="datafile" > <br /><br />
                                <label for="admin[fileselect]" ><?php _e('Scegli il tipo di dati', 'tplus_admin') ?></label>
                                <select name="admin[fileselect]">
                                        <option value="0"></option>
                                        <option value="points"><?php _e('Punteggi', 'tplus_admin') ?></option>
                                        <option value="matches"><?php _e('Incontri', 'tplus_admin') ?></option>
                                        <option value="tours"><?php _e('Tornei', 'tplus_admin') ?></option>
                                        <option value="places"><?php _e('Luoghi', 'tplus_admin') ?></option>
                                        <option value="subs"><?php _e('Iscrizioni', 'tplus_admin') ?></option>
                                </select><br /><br />
                                <select name="admin[fileoperation]">
                                        <option value="0" selected="selected">Append</option>
                                        <option value="1">New</option>
                                </select><br /><br />
                                <button type="submit" name="admin[fupload]" value="ok" class="button action"><?php _e('Upload data','tplus_admin')?></button>
                        </form>
                        <hr />
                        <h3>Gestione DB</h3>
                        <form method="post" action="<?php echo admin_url(); ?>admin.php?page=tennisplus">
                                <button type="submit" name="admin[dbbkp]" value="ok" class="button action">Backup</button>
                                <hr />
                                <!--button type="submit" name="admin[dbreset]" value="ok" class="button action">Reset</button> <br /><br />
                                <input type="checkbox" name="admin[dbresetconfirm]" value="ok" /> Conferma reset<br /-->
                                <?php // _e('ATTENZIONE! Tutti i dati del DB verranno cancellati!', 'tplus_admin')?>
                        </form>
                        <h3>Elenco backup</h3>
                        <?php
                        if(!empty($bkpfiles)){
                                foreach ($bkpfiles as $v){
                                        if(!in_array($v,array('.','..')))
                                                echo '<a href="'.$updir['baseurl'].'/tplusbackups/'.$v.'">'.$v.'<a><br />';
                                }
                        }
                        echo "</div>";
                        echo "<div class='adminfooter'><small>Copyleft .:LoZ:. If you find it nice, have a look <a href='http://www.theloz.net' target='_blank'>here!</a></small></div>";
		}
                
		public function tournaments(){
			global $wpdb;

			$table = new Custom_tplus_Tournament_Table();
			$table->prepare_items();

			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Torneo eliminato: %d', 'tplus_tournaments'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Tornei', 'tplus_tournaments')?> <a class="add-new-h2"
							 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_tournamentsedit&action=edit');?>"><?php _e('Aggiungi nuovo', 'tplus_admin')?></a>
				</h2>
				<?php echo $message; ?>

				<form id="tournaments-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}
		public function places(){
			global $wpdb;

			$table = new Custom_tplus_Places_Table();
			$table->prepare_items();

			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Luogo eliminato: %d', 'tplus_places'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Luoghi', 'tplus_places')?> <a class="add-new-h2"
							 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_placesedit&action=edit');?>"><?php _e('Aggiungi nuovo', 'tplus_tournaments')?></a>
				</h2>
				<?php echo $message; ?>

				<form id="tournaments-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}
		public function matches(){
			global $wpdb;

			$table = new Custom_tplus_Matches_Table();
			$table->prepare_items();

			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Incontro eliminato: %d', 'tplus_matches'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Incontri', 'tplus_matches')?> <a class="add-new-h2"
							 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_matchesedit&action=edit');?>"><?php _e('Aggiungi nuovo', 'tplus_matches')?></a>
				</h2>
				<?php echo $message; ?>

				<form id="matches-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}
		public function points(){
			global $wpdb;

			$table = new Custom_tplus_Points_Table();
			$table->prepare_items();

			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Incontro eliminato: %d', 'tplus_points'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Punteggi', 'tplus_points')?> <a class="add-new-h2"
							 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_pointsedit&action=edit');?>"><?php _e('Aggiungi nuovo', 'tplus_points')?></a>
				</h2>
				<?php echo $message; ?>

				<form id="points-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}
		public function subs(){
			global $wpdb;

			$table = new Custom_tplus_Subs_Table();
			$table->prepare_items();

			$message = '';
			if ('delete' === $table->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Iscrizione eliminata: %d', 'tplus_subs'), count($_REQUEST['id'])) . '</p></div>';
			}
			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Iscrizioni', 'tplus_subs')?> <a class="add-new-h2"
							 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tplus_subsedit&action=edit');?>"><?php _e('Aggiungi nuova iscrizione', 'tplus_subs')?></a>
				</h2>
				<?php echo $message; ?>

				<form id="points-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}
	}
}