<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
/*
The main routine for the phpbb_topics_portal plug in.
*/
/*
    Copyright (C) 2011  macmiller

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
define("sqldb_MULT", 10);
define("sqldb_MAX_READS", -1);

function validate_parms_tp($phpbb_config_location_b,$exclude_forums_b,$return_list_length_b,$topic_text_length_b,$date_format_b) {
   if (!isset($connphpBB_b)) {
      $connphpBB_b = FALSE;
   }
   if(file_exists($phpbb_config_location_b)) {
      include($phpbb_config_location_b);
      $returnvar_b['dbname'] = $dbname;
      $returnvar_b['table_prefix'] = $table_prefix;
   } else {
      $returnvar_b['ind'] = FALSE;
      $returnvar_b['msg'] = 'unable to open indicated phpBB config file:' . $phpbb_config_location_b;
      return $returnvar_b;
   }

   $ex_list = array();
   $nbr_ck = true;
   if(!trim($exclude_forums_b)=="") {
     $ex_list = explode('/',$exclude_forums_b);
           foreach($ex_list as $ex_list_inx => $ex_list_item) {
              if (is_numeric($ex_list_item)) {
                 if (intval($ex_list_item) != floatval($ex_list_item)) {
                    $nbr_ck = false;
                    break;
                 }
              } else {
                 $nbr_ck = false;
              }
           }
           if (!$nbr_ck) {
              $returnvar_b['ind'] = FALSE;
              $returnvar_b['msg'] = "Exclude forum list must either be blank or indicate a list of forums separated by '/', eg. 10/14/15/24 ";
              return $returnvar_b;
           }
   }
   if(trim($exclude_forums_b)=="") {$ex_list[0] = "-1";}
   $returnvar_b['ex_list'] = $ex_list;

   if ((!is_numeric($return_list_length_b)) || ($return_list_length_b == 0)) {
      $returnvar_b['ind'] = FALSE;
      $returnvar_b['msg'] = "Return List Length should be a numeric value greater than 0";
      return $returnvar_b;
   }
   if(is_numeric($return_list_length_b)) {
      if (intval($return_list_length_b) != floatval($return_list_length_b)) {
         $returnvar_b['ind'] = FALSE;
         $returnvar_b['msg'] = "Return List Length should be an integer";
         return $returnvar_b;
      }
   }
   if ((!is_numeric($topic_text_length_b)) || ($topic_text_length_b == 0)) {
      $returnvar_b['ind'] = FALSE;
      $returnvar_b['msg'] = "Topic Text Length should be a numeric value greater than 0";
      return $returnvar_b;
   }
   if(is_numeric($topic_text_length_b)) {
      if (intval($topic_text_length_b) != floatval($topic_text_length_b)) {
         $returnvar_b['ind'] = FALSE;
         $returnvar_b['msg'] = "Topic Text Length should be an integer";
         return $returnvar_b;
      }
   }
   $returnvar_b['date_format'] = $date_format_b;
   if(trim($date_format_b == "")) {
      $returnvar_b['date_format'] = 'j-M-y g:iA';
   }
   // open a phpBB connection
   if (!isset($connphpBB_b) || ($connphpBB_b === FALSE)) {
      $connphpBB_b = mysql_connect($dbhost,$dbuser,$dbpasswd,TRUE);
      mysql_set_charset('utf8',$connphpBB_b);
      $returnvar_b['connphpBB'] = $connphpBB_b;
   }
   if (!$connphpBB_b) {
      $returnvar_b['ind'] = FALSE;
      $returnvar_b['msg'] = 'phpBB mysql_connect failed->' . mysql_error();
      return $returnvar_b;
   }

   $returnvar_b['ind'] = TRUE;
   $returnvar_b['msg'] = "variables pass ck";
   $returnvar_b['connphpBB'] = $connphpBB_b;
   return $returnvar_b;
}

function makeURLformat1($fileFMT1) {
   $returnvarc = array();
   $urlFMT = "";
   $rightPartofURL = str_replace($_SERVER['DOCUMENT_ROOT'],'',$fileFMT1,$nbrREPL);
   if($nbrREPL == 0) {
      $returnvarc['ind'] = FALSE;
      $returnvarc['msg'] = 'error determining URL address with the following unresolved->' . $_SERVER['DOCUMENT_ROOT'];
      return array ($returnvarc, $urlFMT);
   }
   $urlFMT = 'http://' . $_SERVER['SERVER_NAME'] . $rightPartofURL;
   $returnvarc['ind'] = TRUE;
   $returnvarc['msg'] = 'filepath converted to URL';
   return array ($returnvarc, $urlFMT);
}


function get_echo_phpbb_info($phpbb_config_location_a,$phpbb_url_location_a,$exclude_forums_a,$return_list_length_a,$topic_text_length_a,$date_format_a) {
// $logfile = fopen("./zzphpbbtopicslog.txt", "a+");
// fwrite($logfile, "----topics portal Guts start---" . "\n");
   list ($returnvar_a, $phpbb_config_location_URL_a) = makeURLformat1($phpbb_config_location_a);
   if (!$returnvar_a['ind']) {return $returnvar_a;}
   $ht_tab = chr(9);
   $ht_line_prefix = "	                    ";
   $ht_cr = "\n";
   $ht_spaces = "   ";
   // verify parameters, make connection to phpBB db, include phpBB config
   $returnvar_a = validate_parms_tp($phpbb_config_location_a,$exclude_forums_a,$return_list_length_a,$topic_text_length_a,$date_format_a);
   if (!$returnvar_a['ind']) {return $returnvar_a;}
// fwrite($logfile, "A3333" . "\n");
// get forum url
// remove right most stuff up until first /
   $lst_slash = strripos($phpbb_config_location_URL_a, "/");
   if ($lst_slash === FALSE) {
      $returnvar_a['ind'] = FALSE;
      $returnvar_a['msg'] = '/ character not found in config location->' . $phpbb_config_location_URL_a;
      return $returnvar_a;
   } // if (!$sel) {
// use the url_location filled in on the widget screen if given, otherwise
// use the value computed from the file location
   if (trim($phpbb_url_location_a) == '') {
      $forum_url_prefix = substr($phpbb_config_location_URL_a, 0, $lst_slash);
   } else {
      $forum_url_prefix = $phpbb_url_location_a;
   }
      
   $sel = mysql_select_db($returnvar_a['dbname']);
   if (!$sel) {
      $returnvar_a['ind'] = FALSE;
      $returnvar_a['msg'] = mysql_error();
      return $returnvar_a;
   } // if (!$sel) {
   if (sqldb_MAX_READS == -1) {$maxLIMstr ="";}
   else {$maxLIMstr = 'limit ' . sqldb_MAX_READS;}
   $dbTBLprefix = $returnvar_a['table_prefix'];
// fwrite($logfile, "A3433" . "\n");

   $nbr_of_reads = $return_list_length_a * sqldb_MULT;
// $post_count should be a 
   $formatted_exlude = array();
   $raw_excl_list = $returnvar_a['ex_list'];
   foreach($raw_excl_list as $exl_Indx => $exl_Val) {
      $formatted_exclude[$exl_Indx] = "AND po.forum_id != " . $exl_Val;
   }
// fwrite($logfile, "v3333" . "\n");

   $from_line = " FROM " . $dbTBLprefix . "posts"  . " po, " . 
                          $dbTBLprefix . "users"  . " us, " . 
                          $dbTBLprefix . "forums" . " fo, " . 
                          $dbTBLprefix . "topics" . " tt" . "\n";
   $select_line = "SELECT po.post_id, po.topic_id, po.forum_id, po.post_subject, po.post_time, us.username, fo.forum_name, tt.topic_replies, tt.topic_title" . "\n";
   $where_line = "WHERE post_approved = 1" . "\n";
   $and_l1 = "AND us.user_id = po.poster_id" . "\n";
   $and_l2 = "AND po.forum_id = fo.forum_id" . "\n";
   $and_l3 = "AND po.topic_id = tt.topic_id" . "\n";
   $and_excl = "";
   foreach ($formatted_exclude as  $for_ex_indx => $for_ex_val) {
      $and_excl .= $for_ex_val . "\n";
   }
   $ord_by = "ORDER BY post_time DESC" . "\n";
   $lim = "LIMIT 0," . $nbr_of_reads . "\n";
   $sql_command = $select_line . $from_line . $where_line . $and_l1 . $and_l2 . $and_l3 . $and_excl . $ord_by . $lim;

//
// fwrite($logfile, "55333" . "\n");
// fwrite($logfile, "sql->" . $sql_command . "\n");
// fwrite($logfile, "conn->" . $returnvar_a['connphpBB'] . "\n");
   $sql_result = mysql_query($sql_command,$returnvar_a['connphpBB']);
   if (!$sql_result) {
      $returnvar_a['ind'] = FALSE;
      $returnvar_a['msg'] = "error in mysql_query " . $sql_result;
      return($returnvar_a);
   } //if (!$sql_result) {
// fwrite($logfile, "A-433" . "\n");
   $sql_numrows = 0;
   $sql_numrows = mysql_num_rows($sql_result);
   if ($sql_numrows == 0) {
      $returnvar_a['ind'] = FALSE;
      $returnvar_a['msg'] = "NO DATA IN SOURCE phpBB FILEs for Query";
      return $returnvar_a;
   } //if (!$sql_result) {
// table to keep track of hits per album/user
// key to use pid, 
// echo "\n\n" . "	      ";
   $topic_array = array();
   $nbr_sel = 0;
   while ($row = mysql_fetch_array($sql_result)) {
// this loops throug all the records
      $ppost_id = $row["post_id"];
      $ptopic_id = $row["topic_id"];
      $pforum_id = $row["forum_id"];
      $ppost_subject = $row["post_subject"];
      $ppost_time = $row["post_time"];
      $puser_name = $row["username"];

      $pforum_name = $row["forum_name"];
      $ptopic_replies = $row["topic_replies"];
      $ptopic_title = $row["topic_title"];
      $fmt_date = date($returnvar_a['date_format'],$ppost_time);
//    fwrite($logfile, "T1111" . "\n");

      if(!in_array($ptopic_id,$topic_array)) {
         ++$nbr_sel;
//       fwrite($logfile, "T1121" . "\n");
         $topic_array[] = $ptopic_id;
         if($nbr_sel > $return_list_length_a) {
//         fwrite($logfile, "T3121" . "\n");
           break 1;
         }
         $ps = (strlen($ppost_subject) > $topic_text_length_a) ? substr($ppost_subject, 0, $topic_text_length_a) . '...' : $ppost_subject;
         $pst = (strlen($ptopic_title) > $topic_text_length_a) ? substr($ptopic_title, 0, $topic_text_length_a) . '...' : $ptopic_title;
//       fwrite($logfile, "T3421" . "\n");
         $url = $forum_url_prefix . "/viewtopic.php?f={$pforum_id}&amp;t={$ptopic_id}&amp;p={$ppost_id}#p{$ppost_id}";
         echo $ht_cr,
              $ht_tab,
              $ht_tab;
         $output_line = "<a href=" . '"' . $url . '"' . '>' . $pst . '</a>' . '(' . $ptopic_replies . ')' . ' in ' . $pforum_name . ' by ' . $puser_name . ' on ' . $fmt_date . '<br>';
         echo $output_line;
      }   
   }
   if ($nbr_sel == 0) {
      $returnvar_a['ind'] = FALSE;
      $returnvar_a['msg'] = "NO DATA Selected IN SOURCE phpBB FILEs";
      return $returnvar_a;
   } //if (!$sql_result) {
   $returnvar_a['ind'] = TRUE;
   $returnvar_a['msg'] = 'Topics Listed';
   return $returnvar_a;
}
?>