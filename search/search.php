<?php
/*
   +---------------------------------------------------------------+
   |        Classified advert manager for e107 v7xx - by Father Barry
   |
   |        This module for the e107 .7+ website system
   |        Copyright Barry Keal 2004-2010
   |
   |		Licenced for the use of the purchaser only. This is not free
   |		software.
   |
   +---------------------------------------------------------------+
*/
if (!defined('e107_INIT')) {
    exit;
}
include_lan(e_PLUGIN . 'e_classifieds/languages/' . e_LANGUAGE . '.php');

require_once(e_HANDLER . "userclass_class.php");

require_once(e_HANDLER . "date_handler.php");

global $pref, $ECLASSF_PREF, $eclassf_obj;
if (!is_object($eclassf_obj)) {
    require_once(e_PLUGIN . "e_classifieds/includes/eclassifieds_class.php");

    $eclassf_obj = new eclassifieds;
}
$month = date('n');
$day = date('j');
$year = date('Y');
$today = mktime(0, 0, 0, $month, $day, $year);

$return_fields = 't.eclassf_pname,t.eclassf_thumbnail,t.eclassf_name,t.eclassf_id,t.eclassf_desc,t.eclassf_details,t.eclassf_price,t.mlcassf_posted,t.eclassf_user,u.eclassf_catname,v.eclassf_subname,v.eclassf_categoryid,v.eclassf_subid';
$search_fields = array('t.eclassf_name' , 't.eclassf_desc', 't.eclassf_details', 't.eclassf_pname', 't.eclassf_regiment', 'u.eclassf_catname', 'v.eclassf_subname');
$weights = array('2.0', '2.0', '1.5', '2.0', '2.0', '0.5', '0.5');
$no_results = LAN_198;

$where = "find_in_set(eclassf_catclass,'" . USERCLASS_LIST . "') " .
($ECLASSF_PREF['eclassf_approval'] == 1?" and t.eclassf_approved > 0":'') . " and (t.eclassf_expires > " . $today . " or t.eclassf_expires=0 ) and ";
$order = array('t.eclassf_id' => DESC);
$table = "eclassf_ads as t left join #eclassf_subcats as v on v.eclassf_subid = t.eclassf_category left join #eclassf_cats as u on v.eclassf_categoryid = u.eclassf_catid";

$ps = $sch->parsesearch($table, $return_fields, $search_fields, $weights, 'search_eclassf', $no_results, $where, $order);
$text = $ps['text'];
$results = $ps['results'];

function search_eclassf($row)
{
    global $ECLASSF_PREF, $eclassf_obj, $con, $tp;
    if ($row['mlcassf_posted'] > 0) {
        $datestamp = $con->convert_date($row['mlcassf_posted'], 'short');
    } else {
        $datestamp = ECLASSF_75;
    }
    $title = $tp->toHTML($row['eclassf_name'], false);

    $link_id = $row['eclassf_id'];
    // global $eclassf_obj,$tp, $ECLASSF_PREF, $eclassf_thumbnail, $eclassf_from , $eclassf_catid, $eclassf_subid, $eclassf_id;
    $AW = 0;
    $AH = $ECLASSF_PREF['eclassf_thumbheight'];

    if (!empty($row['eclassf_thumbnail']) && file_exists(e_PLUGIN . "e_classifieds/images/classifieds/" . $row['eclassf_thumbnail'])) {
        $img_name = e_PLUGIN . "e_classifieds/image.php?eclassf_picture=" . $row['eclassf_thumbnail'] . "&amp;eclassf_height=$AH&amp;eclassf_watermark=" . $ECLASSF_PREF['eclassf_watermark'];
        $img = "<img src='$img_name' style='border:0px;height:{$ECLASSF_PREF['eclassf_thumbheight']}px;' />" . ' <br />';
    } else {
        $img = '';
    }
    $res['link'] = e_PLUGIN . 'e_classifieds/classifieds.php?0.item.' . $row['eclassf_categoryid'] . '.' . $row['eclassf_subid'] . '.' . $link_id ;
    $res['pre_title'] = $title ?ECLASSF_69 . ' ' : '';
    $res['title'] = $title ? $title : LAN_SEARCH_9;
    $res['summary'] = ECLASSF_70 . ': ' . substr($tp->toHTML($row['eclassf_catname'], false), 0, 30) . ' &mdash; ' . ECLASSF_73 . ': ' . substr($row['eclassf_subname'], 0, 30) ;
    $width = $ECLASSF_PREF['eclassf_thumbheight'] + 60;
    $res['detail'] = "
	<table style='width:100%;' ><tr><td style='text-align:center;height={$width}px;width:{$width}px;' >$img</td><td>" .
    ECLASSF_SCH_01 . ': <b>' . $tp->toHTML($row['eclassf_name'], false) . '</b><br />' .
    ECLASSF_SCH_02 . ': <b>' . $tp->toHTML($row['eclassf_desc'], false) . '</b><br />' .
    ECLASSF_SCH_03 . ': <b>' . $tp->toHTML($row['eclassf_pname'], false) . '</b><br />' .
    ECLASSF_74 . ': <b>' . $tp->toHTML($row['eclassf_price'], false) . '</b>
	</td></tr></table>' . ECLASSF_72 . ': ' . $datestamp;
    return $res;
}

?>