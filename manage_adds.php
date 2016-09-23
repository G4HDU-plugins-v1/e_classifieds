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
// Included when a user is adding or managing an ad.
require_once('../../class2.php');
if (!defined('e107_INIT')) {
    exit;
}
if (!defined('USER_WIDTH')) {
    define(USER_WIDTH, 'width:100%;');
}
require_once(e_HANDLER . "calendar/calendar_class.php");
$eclassf_cal = new DHTML_Calendar(true);
if (!function_exists('headerjs')) {
    // there shouldn't be one but just in case.
    function headerjs()
    {
        global $eclassf_cal;
        return $eclassf_cal->load_files();
    }
}
if (!is_object($eclassf_obj)) {
    require_once(e_PLUGIN . 'e_classifieds/includes/eclassifieds_class.php');
    $eclassf_obj = new eclassifieds;
}
$footer_js[] = e_PLUGIN . 'e_classifieds/includes/e_classifieds.js';
// check class for creating editing ads
if (!$eclassf_obj->eclassf_creator) {
    require_once(HEADERF);
    $ns->tablerender(ECLASSF_54, ECLASSF_53);
    require_once(FOOTERF) ;
    exit;
}
$eclassf_msgtype = 'blank';
$eclassf_msgtext = '<ul>';
require_once(e_HANDLER . 'date_handler.php');
$eclassf_gen = new convert;
require_once(e_HANDLER . "userclass_class.php");
require_once(e_HANDLER . "ren_help.php");
require_once(HEADERF);
$eclassf_originalaction = $_POST['eclassf_originalaction'];
if ($_GET['action'] == 'godo') {
    $catid = (int)$_GET['catid'];
    $actvar = 'edit';
    $eclassf_originalaction = 'edit';
} elseif (isset($_POST['eclassf_save'])) {
    $actvar = 'save';
    $catid = (int)$_POST['catid'];
} elseif (isset($_POST["actvar"])) {
    $actvar = $_POST["actvar"];
    // print_a($_POST);
    if ($actvar == 'new') {
        $eclassf_originalaction = 'new';
    } else {
        $eclassf_originalaction = 'edit';
    }

    $catid = (int)$_POST['catid'];
}
// ******************************************************************************
// *
// *	Delete specified record if admin or if creator
// *
// ******************************************************************************
if ($actvar == 'delete') {
    // We delete the indicated record
    if (isset($_POST['confirm'])) {
        if ($sql->db_Select('eclassf_ads', 'eclassf_user,eclassf_prefix', "where eclassf_id={$catid}", 'nowhere', false)) {
            extract($sql->db_Fetch());
            $tmp = explode('.', $eclassf_user);
            if ($tmp[0] == USERID || $eclassf_obj->eclassf_admin) {
                // add in a check that the correct person can delete it
                if ($sql->db_Delete('eclassf_ads', 'eclassf_id="' . $catid . '"')) {
                    $eclassf_msgtype = 'success';
                    $eclassf_msgtext .= '<li>' . ECLASSF_66 . '</li>';
                    // now delete any pics associated with it.
                    require_once(e_HANDLER . "file_class.php");
                    $eclassf_file = new e_file;
                    if (!empty($eclassf_prefix)) {
                        $eclassf_fprefix = "^" . $eclassf_prefix . ".";
                        unset($eclassf_list);
                        $eclassf_list = $eclassf_file->get_files('images/classifieds/' , $eclassf_fprefix, 'standard', 1);
                        foreach($eclassf_list as $filetogo) {
                            if (strpos(basename($filetogo['fname']), '_') > 0) {
                                unlink('images/classifieds/' . basename($filetogo['fname']));
                            }
                        }
                    }
                } else {
                    // couldn't delete record
                    $eclassf_msgtype = 'error';
                    $eclassf_msgtext .= '<li>' . ECLASSF_151 . '</li>';
                }
            } else {
                // not permitted to delete
                $eclassf_msgtype = 'error';
                $eclassf_msgtext .= '<li>' . ECLASSF_150 . '</li>';
            }
        }
    } else {
        // need to check confirm box
        $eclassf_msgtype = 'warning';
        $eclassf_msgtext .= '<li>' . ECLASSF_65 . '</li>';
    }
    $actvar = "";
}
// ******************************************************************************
// *
// *	process the uploaded files if the upload button clicked or form saved
// *
// ******************************************************************************
if (!empty($_FILES['file_userfile']['name'])) {
    $eclassf_prefix = $_POST['eclassf_prefix'];
    require_once(e_HANDLER . 'upload_handler.php');
    $eclassf_imgpath = e_PLUGIN . 'e_classifieds/images/classifieds/';
    $eclassf_fileoptions = array('max_upload_size' => $ECLASSF_PREF['eclassf_maxpic'] . 'k', 'extra_file_types' => 'jpg,jpeg,png,gif', 'overwrite' => true);
    $eclassf_upresult = process_uploaded_files($eclassf_imgpath, "prefix+" . $eclassf_prefix , $eclassf_fileoptions);
    foreach($eclassf_upresult as $row) {
        if ($row['error'] == 0) {
            $filename = $row['name'];
        }
        $eclassf_msgtype = 'info';
        $eclassf_msgtext .= '<li>' . $row['message'] . '</li>';
        if ($_POST['eclassf_upsubmit'] == 'process') {
            // upload button clicked, do a reedit
            $actvar = 'reedit';
        } else {
            // form is submitted so do save
            $actvar = 'save';
        }
    }
}
// ******************************************************************************
// *
// *	Save button clicked so save record
// *
// ******************************************************************************
if ($actvar == 'save') {
	$_POST['eclassf_counter']='ng';
    if ($ECLASSF_PREF['eclassf_approval'] == 0) {
        // dont need to be approved
        $eclassf_approved = 1;
    } elseif ($eclassf_obj->eclassf_admin) {
        // we are admin so we had a check box for approval
        $eclassf_approved = $_POST['eclassf_approved'];
    } else {
        // not admin and must be approved.
        $eclassf_approved = 0;
    }
    // calc the date but only if an admin (only they can change the expiry date)
    $eclassf_newdate = false;
    if ($eclassf_obj->eclassf_admin && strlen($_POST['eclassf_expires']) > 0) {
        // there are expiry days and there is a date set calc the expiry date according to the posted date
        $eclassf_newdate = true;
        $eclassf_tmp = explode("-", $_POST['eclassf_expires']);
        switch ($ECLASSF_PREF['eclassf_dformat']) {
            case 'Y-m-d':
                $ptime = mktime(0, 0, 1, $eclassf_tmp[1], $eclassf_tmp[2], $eclassf_tmp[0]);
                break;
            case 'm-d-Y':
                $ptime = mktime(0, 0, 1, $eclassf_tmp[0], $eclassf_tmp[1], $eclassf_tmp[2]);
                break;
            default :
                $ptime = mktime(0, 0, 1, $eclassf_tmp[1], $eclassf_tmp[0], $eclassf_tmp[2]);
        }
    } else if ($eclassf_obj->eclassf_admin && (int)$_POST['eclassf_expires'] == 0) {
        $eclassf_newdate = true;
        $ptime = 0;
    } else if ($actvar == 'new' && (int)$ECLASSF_PREF['eclassf_valid'] > 0 && !$eclassf_obj->eclassf_admin) {
        // there are expiry days and a new record and no date set then its from the creation date
        $ptime = time() + ($ECLASSF_PREF['eclassf_valid'] * 86400);
        $eclassf_newdate = true;
    } else {
        // print "L";
        $ptime = 0;
    }
    // are we saving the record or refreshing after uploading pictures
    // print $actvar;
    $eclassf_prefix = $_POST['eclassf_prefix'];
    if ($eclassf_originalaction == 'edit') {
        // $eclassf_approved = (int)$_POST['eclassf_approved']);
        $eclassf_arg = "
		eclassf_name='" . $tp->toDB($_POST['eclassf_name']) . "',
		eclassf_desc='" . $tp->toDB($_POST['eclassf_desc']) . "',
		eclassf_category='" . (int)$_POST['eclassf_category'] . "',
		eclassf_thumbnail='" . $tp->toDB($_POST['eclassf_thumbnail']) . "',
		eclassf_details='" . $tp->toDB($_POST['eclassf_details']) . "',
		eclassf_approved='" . (int)$eclassf_approved . "',
		eclassf_phone='" . $tp->toDB($_POST['eclassf_phone']) . "',
		eclassf_price='" . $tp->toDB($_POST['eclassf_price']) . "',
		eclassf_lastupdated='" . time() . "',
		eclassf_counter='" . $tp->toDB(varset($_POST['eclassf_counter'], '')) . "',";
        if ($eclassf_newdate) {
            // only update the expiry date if we need to
            $eclassf_arg .= 'eclassf_expires="' . $ptime . '",';
        }
        $eclassf_arg .= "
		eclassf_phone='" . $tp->toDB($_POST['eclassf_phone']) . "',
		eclassf_email='" . $tp->toDB($_POST['eclassf_email']) . "',
		eclassf_gallery='" . (int)$_POST['eclassf_gallery'] . "',
		eclassf_location='" . $tp->toDB($_POST['eclassf_location']) . "',
		eclassf_prefix='" . $_POST['eclassf_prefix'] . "'
		WHERE eclassf_id='$catid'";
        $eclassf_res = $sql->db_Update('eclassf_ads', $eclassf_arg , false);
        if ($eclassf_res) {
            $edata_sn = array('action' => 'update', 'user' => USERNAME, 'itemtitle' => $_POST['eclassf_name'], 'catid' => (int)$catid);
            $e_event->trigger("eclassfpost", $edata_sn);
            // $eclassf_msgtext = $eclassf_msgtext;
            if ($ECLASSF_PREF['eclassf_approval'] == 1) {
                $eclassf_msgtype = 'info';
                $eclassf_msgtext .= '<li>' . ECLASSF_48 . '</li>';
            } else {
                $eclassf_msgtype = 'success';
                $eclassf_msgtext .= '<li>' . ECLASSF_68 . '</li>';
            }
        } else {
            $eclassf_msgtype = 'error';
            $eclassf_msgtext = ECLASSF_67 . ' ';
        }
    }

    if ($eclassf_originalaction == 'new') {
        if ($ptime < 0) {
            $ptime = 0;
        }
        if ($sql->db_Select('eclassf_ads', 'eclassf_id', 'where
eclassf_name="' . $tp->toDB($_POST['eclassf_name']) . '" and
eclassf_desc="' . $tp->toDB($_POST['eclassf_desc']) . '" and
eclassf_pname="' . $tp->toDB($_POST['eclassf_pname']) . '" and
eclassf_regiment="' . $tp->toDB($_POST['eclassf_regiment']) . '" and
eclassf_category="' . $tp->toDB($_POST['eclassf_category']) . '" and
eclassf_price="' . $tp->toDB($_POST['eclassf_price']) . '" ', 'nowhere', false)) {
            // duplicate record
            $eclassf_msgtype = 'error';
            $eclassf_msgtext .= '<li>' . ECLASSF_134 . '</li> ';
        } else {
            $eclassf_adid = $sql->db_Insert('eclassf_ads', "
		0, '" . $tp->toDB($_POST['eclassf_name']) . "',
		'" . $tp->toDB($_POST['eclassf_desc']) . "',
		'" . (int)$_POST['eclassf_category'] . "',
		'" . $tp->toDB($_POST['eclassf_thumbnail']) . "',
		'" . $tp->toDB($_POST['eclassf_details']) . "',
		'" . (int)$eclassf_approved . "',
		'" . USERID . "." . $tp->toDB(USERNAME) . "',
		'" . $tp->toDB($_POST['eclassf_phone']) . "',
		'" . $tp->toDB($_POST['eclassf_email']) . "',
		'$ptime',
		'" . time() . "',
		'" . time() . "',
		'" . $tp->toDB($_POST['eclassf_price']) . "','0',
		'" . varset($_POST['eclassf_counter'], '') . "',
		'" . (int)$_POST['eclassf_gallery'] . "',
		'" . $tp->toDB($_POST['eclassf_location']) . "',
		'" . $_POST['eclassf_prefix'] . "'
", false) ;
            $catid = $eclassf_adid;
            if ($eclassf_adid) {
                $edata_sn = array('action' => 'new',
                 'user' => USERNAME,
                 'itemtitle' => $_POST['eclassf_name'],
                 'catid' => (int)$eclassf_adid);
                $e_event->trigger('eclassfpost', $edata_sn);
                if ($ECLASSF_PREF['eclassf_approval'] == 1) {
                    $eclassf_msgtype = 'info';
                    $eclassf_msgtext .= '<li>' . ECLASSF_48 . '</li>';
                } else {
                    $eclassf_msgtype = 'success';
                    $eclassf_msgtext .= '<li>' . ECLASSF_68 . '</li>';
                }
            } else {
                $eclassf_msgtype = 'error';
                $eclassf_msgtext .= '<li>' . ECLASSF_67 . '</li>';
            }

            if (is_object($gold_obj) && $gold_obj->plugin_active('eclassifieds') && $ECLASSF_PREF['eclassf_goldcost'] > 0) {
                // charge gold for advert if gold system in use and active and there is a charge
                // *	Parameters	: 	$gold_param['gold_user_id'] (default no user)
                // *				: 	$gold_param['gold_who_id'] (default no user)
                // *				:	$gold_param['gold_amount'] (default no amount)
                // *				:	$gold_param['gold_type'] (default "adjustment")
                // *				:	$gold_param['gold_action'] 	credit - add to account
                // *												debit - subtract from account
                // *				:	$gold_param['gold_plugin'] (default no plugin)
                // *				:	$gold_param['gold_log'] (default "")
                // *				:	$gold_param['gold_forum'] (default 0)
                $gold_param['gold_user_id'] = USERID;
                $gold_param['gold_who_id'] = 0;
                $gold_param['gold_amount'] = $ECLASSF_PREF['eclassf_goldcost'];
                $gold_param['gold_type'] = ECLASSF_GOLD_06;
                $gold_param['gold_action'] = 'debit';
                $gold_param['gold_plugin'] = 'eclassifieds';
                $gold_param['gold_log'] = ECLASSF_GOLD_05 . ' ' . $_POST['eclassf_name'];
                $gold_param['gold_forum'] = 0;
                $gold_obj->gold_modify($gold_param);
            }
        }
    }
    // delete any pictures that are checked
    $eclassf_prefix = $catid . '_';
    foreach($_POST['delpic'] as $eclassf_togo) {
        $eclassf_togo = basename($eclassf_togo); // ensure clean file name
        if (strpos($eclassf_togo, $_POST['eclassf_prefix']) !== false && file_exists(e_PLUGIN . 'e_classifieds/images/classifieds/' . $eclassf_togo)) {
            unlink('images/classifieds/' . $eclassf_togo);
        }
    }
    $actvar = '';
}

if ($actvar == 'edit' || $actvar == 'reedit' || $actvar == 'new') {
    if ($actvar == 'edit') {
        // editing so get the record
        $sql->db_Select('eclassf_ads', '*', 'eclassf_id = ' . $catid);
        $row = $sql->db_Fetch();
        extract($row);
        // print "doing edit";
    }
    if ($actvar == 'new') {
        // print "doing new";
        // new record so blank
        $eclassf_name = '';
        $eclassf_desc = '';
        $eclassf_category = 0;
        // $eclassf_thumbnail = '';
        $eclassf_approved = false;
        $eclassf_details = '';
        $eclassf_phone = '';
        $eclassf_email = USEREMAIL;
        $eclassf_price = 0;
        $catid = 0;
        $eclassf_expires = 0;
        if ($ECLASSF_PREF['eclassf_valid'] > 0) {
            $eclassf_expires = time() + ($ECLASSF_PREF['eclassf_valid'] * 86400);
        }
        $eclassf_prefix = USERID . time() . '_';
    }
    if ($actvar == 'reedit') {
        // print "doing reedit";
        // reediting so get all the form fields
        $eclassf_name = $_POST['eclassf_name'];
        $eclassf_desc = $_POST['eclassf_desc'];
        $eclassf_pname = $_POST['eclassf_pname'];
        $eclassf_regiment = $_POST['eclassf_regiment'];
        $eclassf_category = $_POST['eclassf_category'];
        $eclassf_thumbnail = $_POST['eclassf_thumbnail'];
        $eclassf_details = $_POST['eclassf_details'];
        $eclassf_phone = $_POST['eclassf_phone'];
        $eclassf_price = $_POST['eclassf_price'];
        $eclassf_counter = $_POST['eclassf_counter'];
        $eclassf_email = $_POST['eclassf_email'];
        $eclassf_location = $_POST['eclassf_location'];
        $eclassf_gallery = $_POST['eclassf_gallery'];
        $eclassf_prefix = $_POST['eclassf_prefix'];
        // get the date entered, convert to mktime so it can be converted back for calendar control
        $eclassf_tmp = explode("-", $_POST['eclassf_expires']);
        switch ($ECLASSF_PREF['eclassf_dformat']) {
            case 'Y-m-d':
                $ptime = mktime(0, 0, 1, $eclassf_tmp[1], $eclassf_tmp[2], $eclassf_tmp[0]);
                break;
            case 'm-d-Y':
                $ptime = mktime(0, 0, 1, $eclassf_tmp[0], $eclassf_tmp[1], $eclassf_tmp[2]);
                break;
            default :
                $ptime = mktime(0, 0, 1, $eclassf_tmp[1], $eclassf_tmp[0], $eclassf_tmp[2]);
                $eclassf_expires = $ptime;
        }
        $catid = (int)$_POST['catid'];
    }
    $eclassf_msgtext .= '</ul>';
    // with the data for the form edit it.
    $eclassf_text = $eclassf_loadcal . "
<form enctype='multipart/form-data' onsubmit='return meclassf_checkok();' id='dataform' method='post' action='" . e_SELF . "'>
	<div>
		<input type='hidden' name='eclassf_originalaction' value='" . $eclassf_originalaction . "' />
		<input type='hidden' name='__referer' value='" . POST_REFERER . "' />
		<input type='hidden' name='catid' value='$catid' />
		<input type='hidden' name='eclassf_prefix' value='$eclassf_prefix' />
	</div>
	<table class='border' style='" . USER_WIDTH . "' >";
    $eclassf_text .= '
		<tr>
			<td class="fcaption" colspan="2" style="text-align:left;" >' . ECLASSF_99 . '&nbsp;</td>
		</tr>
		<tr>
			<td class="forumheader2" colspan="2" style="text-align:left;" >
				<a href="' . e_SELF . '"><img src="' . e_PLUGIN . 'e_classifieds/images/updir.png" alt="logo" style="border:0;"/></a>
			</td>
		</tr>
		<tr>
			<td class="forumheader2" colspan="2" style="text-align:left;" >' . $prototype_obj->message_box($eclassf_msgtype, $eclassf_msgtext) . '</td>
		</tr>';
    // ##############################
    // Read in the category box etc A/R
    $eclassf_catlist = '<select class="tbox" name="eclassf_category">';
    $eclassf_arg = "select * from #eclassf_subcats as s
		left join #eclassf_cats as c on s.eclassf_categoryid = c.eclassf_catid";
    if ($ECLASSF_PREF['eclassf_force_main_cat']) {
        $eclassf_arg .= " WHERE c.eclassf_catid='" . $ECLASSF_PREF['eclassf_force_main_cat'] . "' ";
        if ($ECLASSF_PREF['eclassf_force_sub_cat']) $eclassf_arg .= "AND s.eclassf_subid='" . $ECLASSF_PREF['eclassf_force_sub_cat'] . "' ";
    }
    $eclassf_arg .= ' order by eclassf_catname,eclassf_subname';
    if ($sql->db_Select_gen($eclassf_arg, false)) {
        $eclassf_current = '';
        while ($eclassf_row = $sql->db_Fetch()) {
            if ($eclassf_current != $eclassf_row['eclassf_catname']) {
                $eclassf_current = $eclassf_row['eclassf_catname'];
                $eclassf_catlist .= "<option value='0' disabled='disabled'>" . $eclassf_row['eclassf_catname'] . "</option>";
            }
            $eclassf_catlist .= '<option value="' . $eclassf_row['eclassf_subid'] . '"';
            if ($eclassf_row['eclassf_subid'] == $eclassf_category) {
                $eclassf_catlist .= ' selected="selected"';
            }

            $eclassf_catlist .= "> &nbsp;&raquo;&nbsp;" . $eclassf_row['eclassf_subname'] . "</option>";
            $eclassf_lastupdated_subid = $eclassf_row['eclassf_subid']; // Gets overwritten on terminating condition!
        } // while
        $eclassf_catlist .= "</select>";
        if ($ECLASSF_PREF['eclassf_force_sub_cat']) {
            $eclassf_catlist = "<input type='hidden' name='eclassf_category' value='" . $eclassf_lastupdated_subid . "' />";
        }
    } else {
        $eclassf_catlist .= "<option value='0' >" . ECLASSF_51 . "</select>";
    }

    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_26 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_name' id='eclassf_name' class='tbox' style='width:60%' value='" . $tp->toFORM($eclassf_name) . "' />&nbsp;<i>" . ECLASSF_27 . "</i>
			</td>
		</tr>
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_28 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_desc' class='tbox' style='width:60%' value='" . $tp->toFORM($eclassf_desc) . "' />
			</td>
		</tr>

		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_136 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_location' class='tbox' style='width:60%' value='" . $tp->toFORM($eclassf_location) . "' />
			</td>
		</tr>";
    if ($ECLASSF_PREF['eclassf_force_sub_cat']) { // Hidden subcat value forced
        $eclassf_text .= $eclassf_catlist . "
		";
    } else {
        $eclassf_text .= "

		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_34 . "</td><td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
		{$eclassf_catlist}
			</td>
		</tr>";
    }
    require_once(e_HANDLER . "file_class.php");
    $eclassf_file = new e_file;
    $eclassf_fprefix = "^" . $eclassf_prefix . ".";
    unset($eclassf_list);
    $eclassf_list = $eclassf_file->get_files(e_PLUGIN . 'e_classifieds/images/classifieds/' , $eclassf_fprefix, 'standard', 1);
	// strip any non pictures
	$permitted_pics = array('jpg', 'jpeg', 'png', 'gif');
		    $eclassf_numrecs = count($eclassf_list);
	if ($eclassf_numrecs > 0) {
		foreach($eclassf_list as $key => $value) {
			$pathinfo = pathinfo($value['fname']);
			// print_a($eclassf_list);
			$extn = $pathinfo['extension'];
			if (!in_array($extn, $permitted_pics)) {
				unset($eclassf_list[$key]);
			}
		}
	}
	$eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_115 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>";
    if (count($eclassf_list) > 0) {
        $AH = 60;
        foreach($eclassf_list as $eclassf_line) {
            $img_name = e_PLUGIN . "e_classifieds/image.php?eclassf_picture=" . $eclassf_line['fname'] . "&amp;eclassf_height=$AH&amp;eclassf_watermark=" . $ECLASSF_PREF['eclassf_watermark'];
            $eclassf_text .= "<input class='tbox' name='delpic[]' type='checkbox' value='" . $eclassf_line['fname'] . "' />
		<img src='" . $img_name . "' style='border:0;' alt='" . $eclassf_line['fname'] . "' title='" . $eclassf_line['fname'] . "' /><br />";
        }
    } else {
        $eclassf_text .= '';
    }
    $eclassf_text .= '
			</td>
		</tr>';
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='width:20%' >" . ECLASSF_29 . "</td>
			<td class='forumheader3' style='width:80%'>" . ECLASSF_29a . " " . $ECLASSF_PREF['eclassf_maxpic'] . " " . ECLASSF_29b . "<br />" . ECLASSF_137 . ' ' . $ECLASSF_PREF['eclassf_pich'] . ' ' . ECLASSF_138 . '<br />';
    $eclassf_text .= "<input type='button' name='eclassf_upit' id='eclassf_upit' class='button' onclick='eclassf_showupload();' value='upload a picture' />
				<input type='hidden' id='eclassf_upsubmit' name='eclassf_upsubmit' value='' />
				<div id='eclassf_uparea' style='display: none;'>
					<div id='up_container' >
						<span id='upline' style='white-space:nowrap'>
							<input class='tbox' type='file' name='file_userfile[]' size='50%' />
						</span>
					</div>";
    $eclassf_text .= "
					<table style='width:100%'>
						<tr>
							<td>
								<input type='button' class='button' value='" . ECLASSF_118 . "' onclick=\"duplicateHTML('upline','up_container');\"  />
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				<br />";
    $eclassf_text .= "
				<input type='button' onclick='eclassf_doupload()' name='uppic' class='button' value='" . ECLASSF_122 . "' />";
    $eclassf_text .= "
				</div>
			</td>
		</tr>";
    // chose thumbnail
    $eclassf_fprefix = "^" . $eclassf_prefix . ".";

    $eclassf_list = $eclassf_file->get_files(e_PLUGIN . "e_classifieds\images\classifieds\\" , $eclassf_fprefix, "standard", 1);
	// strip any non pictures
	$permitted_pics = array('jpg', 'jpeg', 'png', 'gif');
	    $eclassf_numrecs = count($eclassf_list);
	if ($eclassf_numrecs > 0) {
		foreach($eclassf_list as $key => $value) {
			$pathinfo = pathinfo($value['fname']);
			// print_a($eclassf_list);
			$extn = $pathinfo['extension'];
			if (!in_array($extn, $permitted_pics)) {
				unset($eclassf_list[$key]);
			}
		}
	}
	if (!file_exists("./images/classifieds/" . $eclassf_thumbnail)) {
        $eclassf_thumbnail = "";
    }
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_120 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input  class='tbox' style='width:50%;text-align:left;' id='eclassf_thumbnail' name='eclassf_thumbnail' type='text' value='" . $tp->toFORM($eclassf_thumbnail) . "' /><br />
		";
    if (count($eclassf_list) > 0) {
        foreach($eclassf_list as $eclassf_line) {
            $img_name = e_PLUGIN . "e_classifieds/image.php?eclassf_picture=" . $eclassf_line['fname'] . "&amp;eclassf_height=$AH&amp;eclassf_watermark=" . $ECLASSF_PREF['eclassf_watermark'];
            $eclassf_text .= "
            	<a href=\"javascript:insertext('" . $eclassf_line['fname'] . "','eclassf_thumbnail','newsicn')\">
					<img src='" . $img_name . "' style='border:0;' alt='" . $eclassf_line['fname'] . "' title='" . $eclassf_line['fname'] . "' />
				</a>";
        }
    } else {
        $eclassf_text .= "";
    }
    $eclassf_text .= "
			</td>
		</tr>";
    if ($ECLASSF_PREF['eclassf_useremail'] == 1) {
        if (!$eclassf_email) $eclassf_email = USEREMAIL;
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_32 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_email' class='tbox' style='width:150px' value='{$eclassf_email}' />&nbsp;<i>" . ECLASSF_27 . "</i>
			</td>
		</tr>";
    } else {
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_32 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='hidden' name='eclassf_email' class='tbox' value='" . USEREMAIL . "' />" . ECLASSF_56 . " " . USEREMAIL . "
			</td>
		</tr>";
    }
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_60 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_price' class='tbox' style='width:150px;text-align:left;' value='" . $tp->toFORM($eclassf_price) . "' />
			</td>
		</tr>";
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_12 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='text' name='eclassf_phone' class='tbox' style='width:150px;text-align:left;' value='" . $tp->toFORM($eclassf_phone) . "' />
			</td>
		</tr>";
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_33 . ":</td>
			<td class='forumheader3'>";
    // HTML Area code
    // <tr><td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_33 . ":</td><td class='forumheader3'>
    // <textarea class='tbox' style='width:80%;vertical-align:top;' rows='8' name='eclassf_details'  onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'>" . $eclassf_details . "</textarea><br />" . ren_help(2) . "
    $insertjs = (!$ECLASSF_PREF['wysiwyg'])?"rows='15' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'":
    "rows='25' style='width:100%' ";
    $eclassf_details = $tp->toForm($eclassf_details);
    $eclassf_text .= "<textarea class='tbox' id='eclassf_details' name='eclassf_details' cols='80'  style='width:95%' {$insertjs}>" . (strstr($eclassf_details, "[img]http") ? $eclassf_details : str_replace("[img]../", "[img]", $eclassf_details)) . "</textarea>";
    if (!$ECLASSF_PREF['wysiwyg']) {
        $eclassf_text .= "<input id='helpb' class='helpbox' type='text' name='helpb' size='100' style='width:95%'/>
			<br />" . display_help("helpb");
    }
    // End HTML Area Code
    $eclassf_text .= "
			</td>
		</tr>";
    // Counter
	/*
    if ($ECLASSF_PREF['eclassf_counter'] == "ALL") {
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_87 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<select class='tbox' name='eclassf_counter'>
					<option value='' " . ($eclassf_counter == ''?"selected='selected'":"") . ">" . ECLASSF_88 . "</option>
					<option value='ng' " . ($eclassf_counter == 'ng'?"selected='selected'":"") . ">Non Graphical</option>
					<option value='cb' " . ($eclassf_counter == 'cb'?"selected='selected'":"") . ">Coloured Blocks</option>
					<option value='crt' " . ($eclassf_counter == 'crt'?"selected='selected'":"") . ">CRTs</option>
					<option value='flame' " . ($eclassf_counter == 'flame'?"selected='selected'":"") . ">Flames</option>
					<option value='floppy' " . ($eclassf_counter == 'floppy'?"selected='selected'":"") . ">Floppy Disks</option>
					<option value='heart' " . ($eclassf_counter == 'heart'?"selected='selected'":"") . ">Hearts</option>
					<option value='jelly' " . ($eclassf_counter == 'jelly'?"selected='selected'":"") . ">Jelly</option>
					<option value='lcd' " . ($eclassf_counter == 'lcd'?"selected='selected'":"") . ">LCD HP Calculator</option>
					<option value='lcdg' " . ($eclassf_counter == 'lcdg'?"selected='selected'":"") . ">LED Green</option>
					<option value='purple' " . ($eclassf_counter == 'purple'?"selected='selected'":"") . ">Purple</option>
					<option value='slant' " . ($eclassf_counter == 'slant'?"selected='selected'":"") . ">Slant</option>
					<option value='snowm' " . ($eclassf_counter == 'snowm'?"selected='selected'":"") . ">Snowman</option>
					<option value='text' " . ($eclassf_counter == 'text'?"selected='selected'":"") . ">Text</option>
					<option value='tree' " . ($eclassf_counter == 'tree'?"selected='selected'":"") . ">Christmas Tree</option>
					<option value='turf' " . ($eclassf_counter == 'turf'?"selected='selected'":"") . ">Turf</option>
				</select>
			</td>
		</tr>";
    }
	   */
    $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_132 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='checkbox' name='eclassf_gallery' class='tbox' style='' value='1' " . ($eclassf_gallery == 1?"checked='checked'":"") . " />
			</td>
		</tr>";
    if ($eclassf_obj->eclassf_admin && $ECLASSF_PREF['eclassf_approval'] == 0) {
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_A74 . ":</td>
			<td class='forumheader3' style='width:80%;text-align:left;vertical-align:top;'>
				<input type='checkbox' name='eclassf_approved' class='tbox' style='' value='1' " .
        ($eclassf_approved == 1?"checked='checked'":"") . " />
			</td>
		</tr>";
    } else {
        if ($ECLASSF_PREF['eclassf_approval'] == 0) {
            $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_84 . "</td>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_129 . "</td>
		</tr>";
        } else {
            $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='vertical-align:top;' >" . ECLASSF_84 . "</td>
			<td class='forumheader3' style='vertical-align:top;' >" . ($actvar == "edit"?ECLASSF_85:ECLASSF_83) . "</td>
		</tr>";
        }
    }
    // -------------------->
    if ($actvar == 'new' && $ECLASSF_PREF['eclassf_valid'] > 0) {
        $eclassf_expires = time() + ($ECLASSF_PREF['eclassf_valid'] * 86400);
    }

    if ($eclassf_obj->eclassf_admin) {
        // calendar options
        $eclassf_cal_options['firstDay'] = 1;
        $eclassf_cal_options['showsTime'] = false;
        $eclassf_cal_options['showOthers'] = false;
        $eclassf_cal_options['weekNumbers'] = false;
        $eclassf_cal_df = "%" . str_replace("-", "-%", $ECLASSF_PREF['eclassf_dformat']);
        $eclassf_cal_options['ifFormat'] = $eclassf_cal_df;
        $eclassf_cal_attrib['class'] = "tbox";
        $eclassf_cal_attrib['name'] = "eclassf_expires";
        $eclassf_cal_attrib['value'] = ($eclassf_expires > 0?date($ECLASSF_PREF['eclassf_dformat'] , $eclassf_expires):"");
        $eclassf_desc = $eclassf_cal->make_input_field($eclassf_cal_options, $eclassf_cal_attrib);
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='text-align:left'>" . ECLASSF_128 . "</td>
			<td class='forumheader3' style='text-align:left'>" . $eclassf_desc . "</td>
		</tr>";
    } else {

    	//$eclassf_expires=
        $eclassf_text .= "
		<tr>
			<td class='forumheader3' style='text-align:left'>" . ECLASSF_128 . "</td>
			<td class='forumheader3' style='text-align:left'>
			<input type='hidden' name='eclassf_expires' value='".date($ECLASSF_PREF['eclassf_dformat'],$eclassf_expires)."' />
			" . ($eclassf_expires > 0?date($ECLASSF_PREF['eclassf_dformat'],$eclassf_expires):ECLASSF_130) . "</td>
		</tr>";
    }
    $eclassf_text .= "
		<tr>
			<td colspan='2' class='forumheader2' style='text-align:left'>
				<input type='submit' class='button' name='eclassf_save' value='" . ECLASSF_127 . "' /></td>
		</tr>
		<tr>
			<td colspan='2' class='fcaption' style='text-align:left'>&nbsp;</td>
		</tr>
	</table>
</form>";
}
// front menu
if ($actvar == '') {
    $eclassf_msgtext .= '</ul>';
    $eclassf_text = '
<form id="dataform"  method="post" action="' . e_SELF . '">
	<div>
		<input type="hidden" name="__referer" value="' . POST_REFERER . '" />
	</div>
	<table class="border" style="' . USER_WIDTH . '" >
		<tr>
			<td class="fcaption" colspan="2">' . ECLASSF_20 . '</td>
		</tr>
		<tr>
			<td class="forumheader2" colspan="2" style="text-align:left" >
	        	<a href="' . e_PLUGIN . 'e_classifieds/classifieds.php"><img src="' . e_PLUGIN . 'e_classifieds/images/updir.png" alt="back" style="border:0px"/></a>
			</td>
		</tr>
		<tr>
			<td class="forumheader2" colspan="2">' . $prototype_obj->message_box($eclassf_msgtype, $eclassf_msgtext) . '</td>
		</tr>
		<tr>
			<td class="forumheader3">' . ECLASSF_20 ;
    if (is_object($gold_obj) && $gold_obj->gold_plugins['eclassifieds'] == 1 && $ECLASSF_PREF['eclassf_goldcost'] > 0) {
        $eclassf_text .= '<br />' . ECLASSF_GOLD_01 . ' ' . $gold_obj->formation($ECLASSF_PREF['eclassf_goldcost']);
    }

    $eclassf_text .= '
			 </td>
			<td class="forumheader3" style="width:70%;text-align:left">
	    		<select class="tbox" name="catid">';
    if ($eclassf_obj->eclassf_admin) {
        $eclassf_sql = '';
    } else {
        $eclassf_sql = "where eclassf_user regexp '^" . USERID . "[.]' ";
    }
    $sql->db_Select('eclassf_ads', 'eclassf_id,eclassf_name', $eclassf_sql . ' order by eclassf_name', 'nowhere', false);
    while ($row = $sql->db_Fetch()) {
        $eyetom = $row['eclassf_id'];
        $eyename = $tp->toFORM($row['eclassf_name']);
        $eclassf_text .= '<option value="' . $eyetom . '" ' .
        ($catid == $eyetom?'selected="selected"':'') . ">$eyename</option>";
        $some = true;
    }

    if (!$some) {
        $eclassf_text .= '<option value="0" ' . ($catid == none?'selected="selected"':'') . '>' . ECLASSF_76 . '</option>';
    }

    $eclassf_text .= '</select><br /><br />';
    if ($some) {
        $eclassf_text .= '<input type="radio" checked="checked" name="actvar" value="edit" />' . ECLASSF_21 . '<br />';
        if (is_object($gold_obj) && $gold_obj->gold_plugins['eclassifieds'] == 1 && $ECLASSF_PREF['eclassf_goldcost'] > 0 && $ECLASSF_PREF['eclassf_goldcost'] > $gold_obj->gold_balance(USERID)) {
            $eclassf_text .= ECLASSF_131 . '<br />';
        } else {
            $eclassf_text .= '<input type="radio" name="actvar" value="new" /> ' . ECLASSF_57 . '<br />';
        }
    } else {
        $eclassf_text .= '<input type="radio" disabled="disabled" name="actvar" value="edit" />' . ECLASSF_21 . '<br />
		<input type="radio" checked="checked" name="actvar" value="new" /> ' . ECLASSF_57 . '<br />';
    }

    $eclassf_text .= '
				<input type="radio" name="actvar" value="delete" /> ' . ECLASSF_22 . '
				<input type="checkbox" name="confirm" style="border:0" class="tbox" />' . ECLASSF_58 . '
	    	</td>
		</tr>
		<tr>
			<td class="forumheader2" colspan="2" style="text-align:left;">
				<input class="tbox" type="submit" value="' . ECLASSF_39 . '" name="doaction" />
			</td>
		</tr>
		<tr>
			<td class="fcaption" colspan="2" style="text-align:left;">&nbsp;</td>
		</tr>
	</table>
</form>';
}
$ns->tablerender(ECLASSF_23, $eclassf_text, 'eclassf_manageads');
require_once(FOOTERF);