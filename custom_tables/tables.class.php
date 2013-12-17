<?php
if(!class_exists('WP_tplus_tables')){
	class WP_tplus_tables{
		public function __construct(){
			global $wpdb;
			$this->places_table = $wpdb->prefix."tplus_places";
			$this->matches_table = $wpdb->prefix."tplus_matches";
			$this->tournaments_table = $wpdb->prefix."tplus_tournaments";
			$this->points_table = $wpdb->prefix."tplus_points";
			$this->dbversion = '1.1';
		}
		public function table_install(){
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
			$sql = "CREATE TABLE ".$this->places_table." (
			`id` int NOT NULL AUTO_INCREMENT,
			`plname` varchar(60) NULL,
			`pldescription` varchar(255) NULL,
			`lat` float NULL,
			`lon` float NULL,
			`plcity` varchar(255) NULL,
			`plprovince` varchar(255) NULL,
			`pladdress` varchar(255) NULL,
			`plphone` varchar(255) NULL,
			`plmail` varchar(255) NULL,
			`plmobile` varchar(255) NULL,
			`plrefperson` varchar(255) NULL,
			`plfield1` varchar(255) NULL,
			`plfield2` varchar(255) NULL,
			`plfield3` varchar(255) NULL,
			`plfield4` varchar(255) NULL,
			`plnote` varchar(255) NULL,
			PRIMARY KEY  (`id`) 
			);";
			dbDelta($sql);
			
			$sql="
			CREATE TABLE ".$this->matches_table." (
			`id` int NOT NULL AUTO_INCREMENT,
			`playerid1` int NULL,
			`playerid2` int NULL,
			`playerid3` int NULL,
			`playerid4` int NULL,
			`placeid` int NULL,
			`matchdate` varchar(255) NULL,
			`tournamentid` int NULL,
			`pointstype` int NULL,
			`setspl1` varchar(20) NULL,
			`setspl2` varchar(20) NULL,
			`gamespl1` varchar(20) NULL,
			`gamespl2` varchar(20) NULL,
			PRIMARY KEY  (`id`) 
			);";
			dbDelta($sql);
			
			$sql="CREATE TABLE ".$this->tournaments_table." (
			`id` int NOT NULL AUTO_INCREMENT,
			`tname` varchar(100) NULL,
			`tdesc` varchar(255) NULL,
			`pointstype` int NULL,
			`placeid` int NULL,
			`date_start` varchar(255) NULL,
			`date_end` varchar(255) NULL,
			PRIMARY KEY  (`id`) 
			);";
			dbDelta($sql);
                        
                        $sql="CREATE TABLE ".$this->points_table." (
                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `pdesc` varchar(255) DEFAULT NULL,
                        `plabel` varchar(30) DEFAULT NULL,
                        `pfullwin` int(1) DEFAULT NULL,
                        `pwin` int(1) DEFAULT NULL,
                        `pdraw` int(1) DEFAULT NULL,
                        `plose` int(1) DEFAULT NULL,
                        `pfulllose` int(1) DEFAULT NULL,
                        PRIMARY KEY  (`id`)
                        );";
			dbDelta($sql);
			
			// save current database version for later use (on upgrade)
			add_option('tplus_db_version', $this->dbversion);
			
			/**
			 * [OPTIONAL] Example of updating to 1.1 version
			 *
			 * If you develop new version of plugin
			 * just increment $tplus_db_version variable
			 * and add following block of code
			 *
			 * must be repeated for each new version
			 * in version 1.1 we change email field
			 * to contain 200 chars rather 100 in version 1.0
			 * and again we are not executing sql
			 * we are using dbDelta to migrate table changes
			 */
			/*
			$installed_ver = get_option('tplus_db_version');
			if ($this->check_update) {
				$sql = "blah blah blah sql
				);";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
			*/
			// notice that we are updating option, rather than adding it
			//update_option('tplus_db_version', $tplus_db_version);
		}
		public function check_update(){
			global $wpdb;
			$installed_ver = get_option('tplus_db_version');
			if($installed_ver != $this->dbversion)
				return true;
			else
				return false;
		}
                public function tables_dump(){
			global $wpdb;
                        $updir = wp_upload_dir();
                        $places_table = $wpdb->prefix."tplus_places";
			$matches_table = $wpdb->prefix."tplus_matches";
			$tournaments_table = $wpdb->prefix."tplus_tournaments";
			$points_table = $wpdb->prefix."tplus_points";
                        //checks on directory
                        $directory = $updir['basedir']."/tplusbackups/";
                        if(!file_exists($directory)) { 
                                mkdir($directory, 0775);
                        }
                        $zip = new ZipArchive();
                        $zipfilename = "tplus_bkp".date("Ymd_His").".zip";
                        $zipfile = $directory.$zipfilename;
                        
                        if ($zip->open($zipfile, ZipArchive::CREATE)!==TRUE) {
                            exit("cannot open <$zipfile>\n");
                        }
                        
                        $data = $wpdb->get_results( "SELECT * FROM ".$places_table,ARRAY_A);
                        $ptmp = sys_get_temp_dir().'/place_tmp.csv';
                        self::tplus_write_csv($data, $ptmp);
                        $zip->addFile($ptmp,'place_tmp.csv');
                        
                        $data = $wpdb->get_results( "SELECT * FROM ".$tournaments_table,ARRAY_A);
                        $ttmp = sys_get_temp_dir().'/tour_tmp.csv';
                        self::tplus_write_csv($data, $ttmp);
                        $zip->addFile($ttmp,'tour_tmp.csv');
                        
                        $data = $wpdb->get_results( "SELECT * FROM ".$matches_table,ARRAY_A);
                        $mtmp = sys_get_temp_dir().'/match_tmp.csv';
                        self::tplus_write_csv($data, $mtmp);
                        $zip->addFile($mtmp,'match_tmp.csv');

                        $data = $wpdb->get_results( "SELECT * FROM ".$points_table,ARRAY_A);
                        $pptmp = sys_get_temp_dir().'/point_tmp.csv';
                        self::tplus_write_csv($data, $pptmp);
                        $zip->addFile($pptmp,'point_tmp.csv');
                        
                        if($zip->close()!==TRUE){
                            die('wrong');    
                        }                        
                        /*header('Content-Type: application/zip');
                        header('Content-disposition: attachment; filename='.$zipfilename);
                        header('Content-Length: ' . filesize($zipfile));
                        readfile($zipfile);*/
                        //print_r( $places );
                        return;
                }
		public function table_uninstall(){
			global $wpdb;
                        delete_option('tplus_db_version');
			$wpdb->query("DROP TABLE IF EXISTS ".$this->places_table);
			$wpdb->query("DROP TABLE IF EXISTS ".$this->matches_table);
			$wpdb->query("DROP TABLE IF EXISTS ".$this->tournaments_table);
			$wpdb->query("DROP TABLE IF EXISTS ".$this->points_table);
		}
		public function table_reset(){
			global $wpdb;
			$wpdb->query("TRUNCATE TABLE ".$this->places_table);
			$wpdb->query("TRUNCATE TABLE ".$this->matches_table);
			$wpdb->query("TRUNCATE TABLE ".$this->tournaments_table);
			$wpdb->query("TRUNCATE TABLE ".$this->points_table);
		}
		function tplus_update_db_check() {
			if (get_site_option('tplus_db_version') != $this->dbversion) {
				$this->table_install();
			}
		}
                static function tplus_write_csv($data,$fname){
                        $tmpcsv = $fname;
                        $fp = fopen($tmpcsv, 'w');
                        foreach ($data as $fields) {
                            fputcsv($fp, $fields);
                        }
                        fclose($fp);
                }
	}
}