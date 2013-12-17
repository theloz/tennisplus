<?php
if(!class_exists('Shortcode_tplus')){
	class Shortcode_tplus{
                function tplus_matches_shortcode_function( $atts ) {
                        global $wpdb;
                        $places_table = $wpdb->prefix."tplus_places";
                        $matches_table = $wpdb->prefix."tplus_matches";
                        $tournaments_table = $wpdb->prefix."tplus_tournaments";
                        $points_table = $wpdb->prefix."tplus_points";
                        $users_table = $wpdb->prefix."users";
                        // Attributes
                        extract( shortcode_atts(
                                array(
                                        'matchid'               => '0',         //bind to a single match, every other option will be ignored
                                        'tournamentid'          => '0',         //bind to a single tournament. 0 means all matches
                                        'issingle'              => '1',         //only single matches by default. 0 for doubles, 2 for any  
                                        'placeid'               => '0',         //if not empty bind the search to a single place
                                        'anyplayerid'           => '0',         //if not 0 look for a player in every position (home, away, single, double)
                                        'p1id'                  => '0',         //look for a player in home position single or double (depending by issingle option) 
                                        'p2id'                  => '0',         //look for a player in away position single or double (depending by issingle option)
                                        'p3id'                  => '0',         //look for a player in home position double
                                        'p4id'                  => '0',         //look for a player in away position double
                                        'limit'                 => '0',         //limits number of results
                                        'fulldetails'           => '0',         //1 all fields, 0 function defined
                                ), $atts )
                        );
                        //let's do some sql logic
                        //if i look for a single place I'll display it
                        if(isset($_GET['pid']) && $_GET['pid']!=""){
                                $pid = (int)stripslashes($_GET['pid']);
                                $wtp = new Shortcode_tplus;
                                $wtp->tplus_places_shortcode_function(array('placeid'=>$pid));
                                /*
                                $q = "SELECT 
                                        plname AS Nome,
                                        pldescription AS Descrizione,
                                        plcity AS Citta,
                                        plprovince AS Provincia,
                                        pladdress AS Indirizzo,
                                        plphone AS Telefono,
                                        plmail AS Email,
                                        plmobile AS Cellulare,
                                        plrefperson AS Referente,
                                        plfield1 AS Campo1,
                                        plfield2 AS Campo2,
                                        plfield3 AS Campo3,
                                        plfield4 AS Campo4,
                                        plnote AS Note
                                        FROM $places_table WHERE id = $pid";
                                $place = $wpdb->get_results($wpdb->prepare($q, 1, 0), ARRAY_A);
                                if( empty($place) ){
                                        self::tplus_shortcode_rendering($place,'empty');
                                }
                                else{
                                        self::tplus_shortcode_rendering($place,'placesingle');
                                }
                                 * 
                                 */
                                return "";
                        }
                        $limit = ( $atts['limit'] == 0 ? 1000 : $atts['limit'] );
                        $q = "SELECT d.display_name AS p1, d.ID AS p1id, d.user_login AS p1login,"
                                . " e.display_name AS p3, e.ID AS p3id, e.user_login AS p3login,"
                                . " f.display_name AS p2, f.ID AS p2id, f.user_login AS p2login,"
                                . " g.display_name AS p4, g.ID AS p4id, g.user_login AS p4login,"
                                . " b.id AS pointid, b.plabel AS pointlabel,"
                                . " c.id AS tourid, c.tname AS tourname,"
                                . " h.id AS placeid, h.plname AS placename,"
                                . " a.matchdate, a.setspl1, a.setspl2, a.gamespl1, a.gamespl2"
                                . " FROM ".$matches_table." a"
                                . " JOIN ".$points_table." b ON b.id=a.pointstype"
                                . " LEFT JOIN ".$tournaments_table." c ON c.id=a.tournamentid AND a.tournamentid != 0"
                                . " JOIN ".$users_table." d ON d.ID=a.playerid1"
                                . " LEFT JOIN ".$users_table." e ON e.ID=a.playerid3 AND a.playerid3 != 0"
                                . " JOIN ".$users_table." f ON f.ID=a.playerid2"
                                . " LEFT JOIN ".$users_table." g ON g.ID=a.playerid4 AND a.playerid4 != 0"
                                . " JOIN ".$places_table." h ON h.id = a.placeid"
                                ;
                        $cond = " WHERE ";
                        do{
                                
                                if($atts['matchid']!=0){
                                        //look for a single match
                                        $cond .= "a.id=".$atts['matchid']." AND ";
                                        $limit = 1;
                                        break;
                                }
                                if( $atts['tournamentid']!=0){
                                       $cond .=  "a.tournamentid=".$atts['tournamentid']." AND ";
                                }
                                if( $atts['anyplayerid']!=0){
                                        $cond .=  "(a.player1id = ".$atts['anyplayerid']." OR a.player2id = ".$atts['anyplayerid']." OR a.player3id = ".$atts['anyplayerid']." OR a.player4id = ".$atts['anyplayerid'].") AND ";
                                }
                                else{
                                        if($atts['p1id']!=0)
                                               $cond .=  "a.player1id = ".$atts['p1id']." AND ";
                                        if($atts['p2id']!=0)
                                               $cond .=  "a.player2id = ".$atts['p2id']." AND ";
                                        if($atts['p3id']!=0 && $atts['issingle']==0)
                                               $cond .=  "a.player3id = ".$atts['p3id']." AND ";
                                        if($atts['p4id']!=0 && $atts['issingle']==0)
                                               $cond .=  "a.player4id = ".$atts['p4id']." AND ";
                                }
                                if( $atts['issingle']==1){
                                        $cond .=  "a.player3id = 0 AND a.player4id = 0 AND ";
                                }
                                break;
                        }while(0);
                        if(strlen($cond)>7)
                                $cond = substr($cond,0,-5);
                        else
                                $cond = '';
                        $q = $q.$cond;
                        $matches = $wpdb->get_results($wpdb->prepare($q, $limit, 0), ARRAY_A);
                        if($atts['fulldetails']==0){
                                $matchdef = array();
                                $i = 0;
                                foreach($matches as $v){
                                        $matchdef[$i]['Giocatore 1'] = "<a href=\"".site_url()."/forums/user/".$v['p1login']."\">".$v['p1']."</a>";
                                        if($v['p3']!="")
                                                $matchdef[$i]['Giocatore 3'] = "<a href=\"forums/user/".$v['p3login']."\">".$v['p3']."</a>";
                                        $matchdef[$i]['Giocatore 2'] = "<a href=\"".site_url()."/forums/user/".$v['p2login']."\">".$v['p2']."</a>";
                                        if($v['p4']!="")
                                                $matchdef[$i]['Giocatore 4'] = "<a href=\"forums/user/".$v['p4login']."\">".$v['p4']."</a>";
                                        $matchdef[$i]['Data Incontro'] = date("d M Y H:i", strtotime($v['matchdate']));
                                        $matchdef[$i]['Campo'] = "<a href=\"".get_permalink().(strpos(get_permalink(),"?")>0 ? "&" : "?" )."pid=".$v['placeid']."\">".$v['placename']."</a>";
                                        $matchdef[$i]['Risultato'] = $v['setspl1']." - ".$v['setspl2']."<br />(".$v['pointlabel'].")";
                                        $g1 = explode(":",$v['gamespl1']);
                                        $g2 = explode(":",$v['gamespl2']);
                                        $gstr = '';
                                        for($n=0;$n<count($g1);$n++){
                                                $gstr .= $g1[$n].":".$g2[$n]." - ";
                                        }
                                        $gstr = substr($gstr,0,-3);
                                        $matchdef[$i]['Punteggio'] = $gstr;
                                        
                                        $i++;
                                }
                        }
                        else{
                                $matchdef = $matches;
                        }
                        if(empty($matches)){
                                self::tplus_shortcode_rendering($matchdef,'empty');
                        }
                        else if(count($matches)==1){
                                $fields = array_keys($matchdef[0]);
                                self::tplus_shortcode_rendering($matchdef,'tablesingle',$fields);
                        }
                        else{
                                $fields = array_keys($matchdef[0]);
                                self::tplus_shortcode_rendering($matchdef,'table',$fields);
                        }
                        // Code
                        return "";
                }
                //manage friendly match
                function tplus_friendly_match_shortcode_function( $atts ){
                        
                }
                //shows a place
                function tplus_places_shortcode_function( $atts ) {
                        global $wpdb;
                        $places_table = $wpdb->prefix."tplus_places";
                        // Attributes
                        extract( shortcode_atts(
                                array(
                                        'placeid'               => '0',         //if not empty bind the search to a single place
                                ), $atts )
                        );
                        $pid = $atts['placeid'];
                        $q = "SELECT 
                                plname AS Nome,
                                pldescription AS Descrizione,
                                plcity AS Citta,
                                plprovince AS Provincia,
                                pladdress AS Indirizzo,
                                plphone AS Telefono,
                                plmail AS Email,
                                plmobile AS Cellulare,
                                plrefperson AS Referente,
                                plfield1 AS Campo1,
                                plfield2 AS Campo2,
                                plfield3 AS Campo3,
                                plfield4 AS Campo4,
                                plnote AS Note
                                FROM $places_table WHERE id = $pid";
                        $place = $wpdb->get_results($wpdb->prepare($q, 1, 0), ARRAY_A);
                        if( empty($place) ){
                                self::tplus_shortcode_rendering($place,'empty');
                        }
                        else{
                                self::tplus_shortcode_rendering($place,'placesingle');
                        }
                        return "";
                }
                //shows a tournaments
                function tplus_tournaments_shortcode_function( $atts ) {
                        global $wpdb;
                        $places_table = $wpdb->prefix."tplus_places";
                        $matches_table = $wpdb->prefix."tplus_matches";
                        $tournaments_table = $wpdb->prefix."tplus_tournaments";
                        $points_table = $wpdb->prefix."tplus_points";
                        $users_table = $wpdb->prefix."users";
                        // Attributes
                        extract( shortcode_atts(
                                array(
                                        'tourid'               => '0',         //if not empty bind the search to a single tournament
                                        'limit'                 => '0',         //limits number of results, 0 for all
                                        'fulldetails'           => 'no',         //1 all matches, 0 no match
                                ), $atts )
                        );
                        $q = "SELECT 
                                a.id AS tid, a.tname AS Nome, a.tdesc AS Descrizione, a.date_start AS 'Data Inizio', a.date_end AS 'Data fine',
                                b.id AS placeid, b.plname AS Luogo,
                                c.id AS pointid, c.plabel AS Punteggio
                                FROM $tournaments_table a
                                JOIN $places_table b ON a.placeid = b.id
                                JOIN $points_table c ON a.pointstype = c.id
                                WHERE a.id =".$atts['tourid'];
                        $tournaments = $wpdb->get_results($wpdb->prepare($q, 1, 0), ARRAY_A);
                        if($atts['fulldetails']=='yes'){
                                $wpt = new Shortcode_tplus;
                                $matches = $wpt->tplus_matches_shortcode_function(array('tournamentid'=>$atts['tourid'], 'limit'=>$atts['limit']));
                                echo "pippo";
                                print_r($matches);
                        }
                        if( empty($tournaments) ){
                                self::tplus_shortcode_rendering($tournaments,'empty');
                        }
                        else{
                                self::tplus_shortcode_rendering($tournaments,'tournament');
                                if(!empty($matches))
                                        self::tplus_shortcode_rendering($matches,'table');
                        }
                        return "";
                }
                //manage results display
                static function tplus_shortcode_rendering($data, $mode,$fields = array() ){
                        switch($mode){
                                case 'tablesingle':
                                        $tb = "<div class=\"tp_clear\">";
                                        $tb .= "<table class=\"tp_table tp_matches_table_single\">";
                                        $tb .= "<thead><tr class=\"tp_tr_thead\">";
                                        foreach($fields as $f){
                                                $tb .= "<th class=\"tplus_th\">".$f."</th>";
                                        }
                                        $tb .= "</tr></thead>";
                                        $c=1;
                                        foreach($data as $k => $d){
                                                $tb .= "<tr class=\"tplus_tr_row".($c%2==0 ? ' tp_even' : ' tp_odd')."\">";
                                                foreach($d as $kk => $val){
                                                        if(in_array($kk, $fields) )
                                                                $tb .= "<td class=\"tplus_td\">".$val."</td>";
                                                }
                                                $c++;
                                                $tb .= "</tr>";
                                        }
                                        $tb .= "</table></div>";
                                        $tb .= "";
                                        echo $tb;
                                        break;
                                case 'table':
                                        $tb = "<div class=\"tp_clear\">";
                                        $tb .= "<table class=\"tp_table tp_matches_table\">";
                                        $tb .= "<thead><tr class=\"tp_tr_thead\">";
                                        foreach($fields as $f){
                                                $tb .= "<th class=\"tplus_th\">".$f."</th>";
                                        }
                                        $tb .= "</tr></thead>";
                                        $c=1;
                                        foreach($data as $k => $d){
                                                $tb .= "<tr class=\"tplus_tr_row".($c%2==0 ? ' tp_even' : ' tp_odd')."\">";
                                                foreach($d as $kk => $val){
                                                        if(in_array($kk, $fields) )
                                                                $tb .= "<td class=\"tplus_td\">".$val."</td>";
                                                }
                                                $c++;
                                                $tb .= "</tr>";
                                        }
                                        $tb .= "</table></div>";
                                        $tb .= "";
                                        echo $tb;
                                        break;
                                case 'placesingle':
                                case 'tournament':
                                        $tb = "<div class=\"tp_clear\">";
                                        $place = $data[0];
                                        $tb .= "<h3>".$place['Nome']."</h3>";
                                        $tb .= "<table class=\"tp_table tp_matches_place\"><tbody>";
                                        foreach( $place as $k => $v ){
                                                if( $v != '' && $k != "Nome" ){
                                                        $tb .= "<tr>";
                                                        $tb .= "<td class=\"tplus_td2\"><strong>$k</strong></td><td class=\"tplus_td2\">$v</td>";
                                                        $tb .= "</tr>";
                                                }
                                        }
                                        $tb .= "</tbody></table>";
                                        $tb .= "</div>";
                                        echo $tb;
                                        break;
                                case 'debug':
                                        echo "<pre>";
                                        print_r($data);
                                        echo "</pre>";
                                        break;
                                case 'empty':
                                        echo "<div class=\"tp_clear\">".__('Nessun risultato', 'tplus_shortcodes')."</div>";
                                        break;
                        }
                }
                
        }
}

