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
global $eclassf_obj, $ECLASSF_PREF;
if (!is_object($eclassf_obj)) {
    require_once(e_PLUGIN . 'e_classifieds/includes/eclassifieds_class.php');
    $eclassf_obj = new eclassifieds;
}
if ($ECLASSF_PREF['eclassf_approval'] == 1) {
    $eclassf_approve = $sql->db_Count('eclassf_ads', '(*)', 'WHERE eclassf_approved="0"', false);
}else {
    $eclassf_approve = 0;
}
$text .= '<div style="padding-bottom: 2px;">
<img src="' . e_PLUGIN . 'e_classifieds/images/icon_16.png" style="width:16px;height:16px;vertical-align:bottom;border:0;" alt="" /> ';
if (empty($eclassf_approve)) {
    $eclassf_approve = 0;
}
if ($eclassf_approve) {
    $text .= '<a href="' . e_PLUGIN . 'e_classifieds/admin_submit.php">' . ECLASSF_A51 . ': ' . $eclassf_approve . '</a>';
}else {
    $text .= ECLASSF_A51 . ': ' . $eclassf_approve;
}

$text .= '</div>';

?>