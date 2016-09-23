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
// Plugin info -------------------------------------------------------------------------------------------------------
include_lan(e_PLUGIN . 'e_classifieds/languages/' . e_LANGUAGE . '.php');
$eplug_name = 'e_Classifieds';
$eplug_version = '2.3';
$eplug_author = 'Father Barry';

$eplug_url = 'http://www.keal.me.uk/';
$eplug_email = '';
$eplug_description = ECLASSF_P01;
$eplug_compatible = 'e107v7';
$eplug_readme = 'admin_readme.php'; // leave blank if no readme file
$eplug_compliant = true;
$eplug_status = true;
$eplug_latest = true;
// Name of the plugin's folder -------------------------------------------------------------------------------------
$eplug_folder = 'e_classifieds';
// Mane of menu item for plugin ----------------------------------------------------------------------------------
$eplug_menu_name = '';
// Name of the admin configuration file --------------------------------------------------------------------------
$eplug_conffile = 'admin_config.php';
// Icon image and caption text ------------------------------------------------------------------------------------
$eplug_icon = $eplug_folder . '/images/icon_32.png';
$eplug_icon_small = $eplug_folder . '/images/icon_16.png';
$eplug_caption = 'Classified Adverts';
// List of preferences -----------------------------------------------------------------------------------------------
// preferences now handled in class
// List of table names -----------------------------------------------------------------------------------------------
$eplug_sql = file_get_contents(e_PLUGIN . "{$eplug_folder}/classifieds_sql.php");
preg_match_all("/CREATE TABLE (.*?)\(/i", $eplug_sql, $matches);
$eplug_table_names = $matches[1];
// List of sql requests to create tables -----------------------------------------------------------------------------
// Apply create instructions for every table you defined in locator_sql.php --------------------------------------
// MPREFIX must be used because database prefix can be customized instead of default e107_
$eplug_tables = explode(';', str_replace('CREATE TABLE ', 'CREATE TABLE ' . MPREFIX, $eplug_sql));
for ($i = 0; $i < count($eplug_tables); $i++)
{
    $eplug_tables[$i] .= ';';
}
array_pop($eplug_tables); // Get rid of last (empty) entry

// Create a link in main menu (yes=TRUE, no=FALSE) -------------------------------------------------------------
$eplug_link = true;
$eplug_link_name = 'Classifieds';
$eplug_link_url = e_PLUGIN . 'e_classifieds/classifieds.php';
// Text to display after plugin successfully installed ------------------------------------------------------------------
$eplug_done = ECLASSF_P03;
// upgrading ...
$upgrade_add_prefs = '';

$upgrade_remove_prefs = '';

$old_version = $pref['plug_installed']['e_classifieds'];
if (version_compare($old_version, '2.3', '<')) {
	$upgrade_alter_tables = array(
	"alter table ".MPREFIX."eclassf_ads add column eclassf_prefix char(32) NULL after eclassf_location",
	"update ".MPREFIX."eclassf_ads set eclassf_prefix = CONCAT(CAST(eclassf_id AS CHAR),'_') where eclassf_prefix is null or eclassf_prefix=''");
}

$eplug_upgrade_done = ECLASSF_P04;
if (!function_exists('e_classifieds_uninstall'))
{
    function e_classifieds_uninstall()
    {
        global $sql;
        $sql->db_Delete('rate', 'rate_table="classifieds"');
        $sql->db_Delete('core', 'e107_name="classifieds"');
    }
}

?>