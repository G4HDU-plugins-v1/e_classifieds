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
if (!defined('USER_WIDTH')) {
    define(USER_WIDTH, 'width:100%');
}
global $eclassf_shortcodes;

#if (file_exists('./images/logo.png')) {
 #   $eclassf_logo = '<img src="./images/logo.png" alt="logo" style="border:0;"/>';
#}
// *******************************************************************************************
// *
// *	Display advert page
// *
// *******************************************************************************************
$sc_style['ECLASSF_SENDPM']['pre'] = '&nbsp;&nbsp;';
$sc_style['ECLASSF_SENDPM']['post'] = '';
$sc_style['ECLASSF_LOGO']['pre'] = '
	<tr>
		<td class="forumheader2" style="text-align:center;" colspan="'.$eclassf_colspan.'"> ';
$sc_style['ECLASSF_LOGO']['post'] = '
		</td>
	</tr>';
if (!isset($ECLASSF_ITEM_HEAD)) {
    $ECLASSF_ITEM_HEAD = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption" colspan="2">{ECLASSF_ITEMHEAD}</td>
	</tr>
	{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="text-align:left;" colspan="2">{ECLASSF_ITEMUPDIR}&nbsp;&nbsp;{ECLASSF_ITEMPRINT}&nbsp;&nbsp;{ECLASSF_ITEMEMAIL}{ECLASSF_SENDPM}&nbsp;&nbsp;{ECLASSF_EDIT}</td>
	</tr>';
}
// shortcode prefixes
// ECLASSF_ITEMPICTURE
$sc_style['ECLASSF_ITEMPICTURE']['pre'] = '
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_9 . '</td>
		<td class="forumheader3" id="eclassf_piccell">';
$sc_style['ECLASSF_ITEMPICTURE']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMVIEWS
$sc_style['ECLASSF_ITEMVIEWS']['pre'] = '
		<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_86 . '</td>
		<td class="forumheader3">';
$sc_style['ECLASSF_ITEMVIEWS']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMDETAILS
$sc_style['ECLASSF_ITEMDETAILS']['pre'] = '
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_10 . '</td>
		<td class="forumheader3">';
$sc_style['ECLASSF_ITEMDETAILS']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMPHONE
$sc_style['ECLASSF_ITEMPHONE']['pre'] = '
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_12 . '</td>
		<td class="forumheader3"> ';
$sc_style['ECLASSF_ITEMPHONE']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMPRICE
$sc_style['ECLASSF_ITEMPRICE']['pre'] = '
		<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_60 . '</td>
		<td class="forumheader3"> ';
$sc_style['ECLASSF_ITEMPRICE']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMEXPIRES
$sc_style['ECLASSF_ITEMEXPIRES']['pre'] = '
		<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_135 . '</td>
		<td class="forumheader3"> ';
$sc_style['ECLASSF_ITEMEXPIRES']['post'] = '
		</td>
	</tr>';
// ECLASSF_ITEMLOCATION
$sc_style['ECLASSF_ITEMLOCATION']['pre'] = '
		<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_136 . '</td>
		<td class="forumheader3"> ';
$sc_style['ECLASSF_ITEMLOCATION']['post'] = '
		</td>
	</tr>';
$sc_style['ECLASSF_ITEMPOSTEREMAIL']['pre'] = '
		<tr>
			<td class="forumheader3" style="width:20%;">' . ECLASSF_13 . '</td>
			<td class="forumheader3">';
$sc_style['ECLASSF_ITEMPOSTEREMAIL']['post'] = '&nbsp;
			</td>
		</tr>';
if (!isset($ECLASSF_ITEM_DETAIL)) {
    $ECLASSF_ITEM_DETAIL = '
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_7 . '</td>
		<td class="forumheader3">{ECLASSF_ITEMNAME}&nbsp;</td>
	</tr>
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_8 . '</td>
		<td class="forumheader3">{ECLASSF_ITEMDESC}&nbsp;</td>
	</tr>
	{ECLASSF_ITEMLOCATION}
	{ECLASSF_ITEMPICTURE}
	{ECLASSF_ITEMDETAILS}
	<tr>
		<td class="forumheader3" style="width:20%;">' . ECLASSF_11 . '</td>
		<td class="forumheader3">{ECLASSF_POSTERNAME}</td>
	</tr>
	{ECLASSF_ITEMPHONE}
    {ECLASSF_ITEMPOSTEREMAIL}
	{ECLASSF_ITEMPRICE}
	{ECLASSF_ITEMVIEWS}
	{ECLASSF_ITEMEXPIRES}';
}
if (!isset($ECLASSF_ITEM_NONE)) {
    $ECLASSF_ITEM_NONE .= '
			<tr>
				<td class="forumheader3" colspan="2"">' . ECLASSF_44 . '</td>
			</tr>';
}
// *******************************************************************************************
// *
// *	List Sub Categories Page
// *
// *******************************************************************************************
if (!isset($ECLASSF_SUB_HEAD)) {
    // Template if not using drop downs
    $ECLASSF_SUB_HEAD = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption" colspan="{ECLASSF_COLSPAN}">{ECLASSF_SUBHEAD}</td>
	</tr>
		{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="width:30%;text-align:left;" colspan="{ECLASSF_COLSPAN}">{ECLASSF_SUBUPDIR}</td>
	</tr>';

    if ($ECLASSF_PREF['eclassf_icons'] > 0) {
        $ECLASSF_SUB_HEAD .= '
	<tr>
		<td class="forumheader2" style="width:10%;">&nbsp;</td>
		<td class="forumheader2" style="width:60%;"><b>' . ECLASSF_5 . '</b></td>
		<td class="forumheader2" style="width:30%;"><b>' . ECLASSF_6 . '</b></td>
	</tr>';
    } else {
        $ECLASSF_SUB_HEAD .= '
	<tr>
		<td class="forumheader2" style="width:70%;"><b>' . ECLASSF_5 . '</b></td>
		<td class="forumheader2" style="width:30%;"><b>' . ECLASSF_6 . '</b></td>
	</tr>';
    }
}
if (!isset($ECLASSF_SUB_DETAIL)) {
    // Template if not using drop downs
    if ($ECLASSF_PREF['eclassf_icons'] > 0) {
        $ECLASSF_SUB_DETAIL = '
	<tr>
		<td class="forumheader3" style="width:10%;text-align:left;">{ECLASSF_SUBICON}</td>
		<td class="forumheader3" style="width:60%;text-align:left;">{ECLASSF_SUBNAME}</td>
		<td class="forumheader3" style="width:30%;text-align:left;">{ECLASSF_SUBADVERTS}</td>
	</tr>';
    } else {
        $ECLASSF_SUB_DETAIL = '
	<tr>
		<td class="forumheader3" style="width:70%;text-align:left;">{ECLASSF_SUBNAME}</td>
		<td class="forumheader3" style="width:30%;text-align:left;">{ECLASSF_SUBADVERTS}</td>
	</tr>';
    }
}
if (!isset($ECLASSF_SUB_HEADDROP)) {
    // Template if using drop downs
    $ECLASSF_SUB_HEADDROP = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption" colspan="{ECLASSF_COLSPAN}">{ECLASSF_SUBHEAD}</td>
	</tr>
			{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="text-align:left;" colspan="{ECLASSF_COLSPAN}">{ECLASSF_SUBUPDIR}</td>
	</tr>
	<tr>
		<td class="forumheader2" style="width:100%;"><strong>' . ECLASSF_5 . '</strong></td>
	</tr>	';
}
if (!isset($ECLASSF_SUB_DETAILDROP)) {
    // Template if using drop downs
    $ECLASSF_SUB_DETAILDROP = '
	<tr>
		<td class="forumheader3" style="width:70%;text-align:left;">{ECLASSF_SELECTOR} {ECLASSF_SUBMIT}</td>
	</tr>';
}
if (!isset($ECLASSF_SUB_NOAD)) {
    $ECLASSF_SUB_NOAD = '
	<tr>
		<td class="forumheader3" colspan="$eclassf_colspan">' . ECLASSF_51 . '</td>
	</tr>';
}
if (!isset($ECLASSF_SUB_FOOTER)) {
    $ECLASSF_SUB_FOOTER = '
</table>';
}
// *******************************************************************************************
// *
// *	List Categories Page
// *
// *******************************************************************************************
if (!isset($ECLASSF_CAT_HEAD)) {
    $ECLASSF_CAT_HEAD = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption" colspan="{ECLASSF_COLSPAN}">' . ECLASSF_4 . '</td>
	</tr>
			{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="text-align:left;" colspan="{ECLASSF_COLSPAN}"><img src="./images/blank.png" alt="" style="border:0;"/></td>
	</tr>';
    if ($ECLASSF_PREF['eclassf_icons'] > 0) {
        $ECLASSF_CAT_HEAD .= '
    <tr>
		<td class="forumheader2" style="width:10%;">&nbsp;</td>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_2 . '</strong></td>
		<td class="forumheader2" style="width:40%;"><strong>' . ECLASSF_3 . '</strong></td>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_5 . '</strong></td>
	</tr>';
    } else {
        $ECLASSF_CAT_HEAD .= '
    <tr>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_2 . '</strong></td>
		<td class="forumheader2" style="width:50%;"><strong>' . ECLASSF_3 . '</strong></td>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_5 . '</strong></td>
	</tr>';
    }
}
if (!isset($ECLASSF_CAT_DETAIL)) {
    if ($ECLASSF_PREF['eclassf_icons'] > 0) {
        $ECLASSF_CAT_DETAIL = '
	<tr>
		<td class="forumheader3" style="width:10%;">{ECLASSF_CATICON}</td>
		<td class="forumheader3" style="width:25%;">{ECLASSF_CATNAME}</td>
		<td class="forumheader3" style="width:40%;">{ECLASSF_CATDESC}</td>
		<td class="forumheader3" style="width:25%;">{ECLASSF_CATSUB}</td>
	</tr>';
    } else {
        $ECLASSF_CAT_DETAIL = '
	<tr>
		<td class="forumheader3" style="width:25%;">{ECLASSF_CATNAME}</td>
		<td class="forumheader3" style="width:50%;">{ECLASSF_CATDESC}</td>
		<td class="forumheader3" style="width:25%;">{ECLASSF_CATSUB}</td>
	</tr>';
    }
}
if (!isset($ECLASSF_CAT_FOOTER)) {
    $ECLASSF_CAT_FOOTER = '
</table>';
} // *******************************************************************************************
// *
// *	List adverts page
// *
// *******************************************************************************************
if (!isset($ECLASSF_LIST_HEAD)) {
    // Heading for list adverts page
    $ECLASSF_LIST_HEAD = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption" colspan="{ECLASSF_COLSPAN}">{ECLASSF_LIST_CATNAME}</td>
	</tr>
	{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="text-align:left;" colspan="{ECLASSF_COLSPAN}">{ECLASSF_LISTUPDIR}</td>
	</tr>';
    if ($ECLASSF_PREF['eclassf_thumbs'] > 0) {
        // If we are using thumbnails then extra column
        $ECLASSF_LIST_HEAD .= '
    <tr>
		<td class="forumheader2" style="">&nbsp;</td>
		<td class="forumheader2" style="width:22%;"><strong>' . ECLASSF_15 . '</strong></td>
		<td class="forumheader2" style="width:22%;"><strong>' . ECLASSF_60 . '</strong></td>
		<td class="forumheader2" style="width:22%;"><strong>' . ECLASSF_11 . '</strong></td>
		<td class="forumheader2" style="width:15%;text-align:right;"><strong>' . ECLASSF_16 . '</strong></td>
	</tr>';
    } else {
        $ECLASSF_LIST_HEAD .= '
    <tr>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_15 . '</strong></td>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_60 . '</strong></td>
		<td class="forumheader2" style="width:25%;"><strong>' . ECLASSF_11 . '</strong></td>
		<td class="forumheader2" style="width:15%;text-align:right;"><strong>' . ECLASSF_16 . '</strong></td>
	</tr>';
    }
}
if (!isset($ECLASSF_LIST_DETAIL)) {
    // The individual rows of adverts
    if ($ECLASSF_PREF['eclassf_thumbs'] > 0) {
        $ECLASSF_LIST_DETAIL .= '
	<tr>
		<td class="forumheader3" style="text-align:center;">{ECLASSF_LISTTHUMBS}</td>
		<td class="forumheader3" style="">{ECLASSF_LISTNAME}</td>
		<td class="forumheader3" style="">{ECLASSF_LISTPRICE}</td>
		<td class="forumheader3" style="">{ECLASSF_LISTPOSTER}</td>
		<td class="forumheader3" style="text-align:right;">{ECLASSF_POSTED}</td>
	</tr>';
    } else {
        $ECLASSF_LIST_DETAIL .= '
	<tr>
		<td class="forumheader3" style="">{ECLASSF_LISTNAME}</td>
		<td class="forumheader3" style="">{ECLASSF_LISTPRICE}</td>
		<td class="forumheader3" style="">{ECLASSF_LISTPOSTER}</td>
		<td class="forumheader3" style="text-align:right;">{ECLASSF_POSTED}</td>
	</tr>';
    }
}

if (!isset($ECLASSF_LIST_NORES)) {
    // Error message for no adverts found
    $ECLASSF_LIST_NORES = '
	<tr>
		<td class="forumheader3" colspan="{ECLASSF_COLSPAN}">' . ECLASSF_52 . '</td>
	</tr>';
}
// *******************************************************************************************
// *
// *	List of adverts page footer
// *
// *******************************************************************************************

if (!isset($ECLASSF_LIST_FOOTER)) {
	$sc_style['ECLASSF_LISTNEXTPREV']['pre'] = '
	<tr>
		<td class="forumheader2" colspan="'.$eclassf_colspan.'">';
	$sc_style['ECLASSF_LISTNEXTPREV']['post'] = '
		</td>
	</tr>';
    // List adverts footer - shows next prev if there are too many records
    $ECLASSF_LIST_FOOTER = '{ECLASSF_LISTNEXTPREV}
</table>';
}
// *******************************************************************************************
// *
// *	Standard page footer
// *
// *******************************************************************************************
if (!isset($ECLASSF_FOOTER)) {
    $ECLASSF_FOOTER = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="forumheader2" style="width:100%;">{ECLASSF_TERMSLINK} {ECLASSF_MANAGE}</td>
	</tr>
	<tr>
		<td class="fcaption" style="width:100%;">&nbsp;</td>
	</tr>
</table>';
}
// *******************************************************************************************
// *
// *	Terms and conditions
// *
// *******************************************************************************************
if (!isset($ECLASSF_TC)) {
    $ECLASSF_TC = '
<table class="fborder" style="' . USER_WIDTH . ';">
	<tr>
		<td class="fcaption">' . ECLASSF_41 . '</td>
	</tr>
	<tr>
		<td class="forumheader2" style="width:30%;text-align:left;" >
			{ECLASSF_UPDIRTC}
		</td>
	</tr>
		{ECLASSF_LOGO}
	<tr>
		<td class="forumheader2" style="width:70%;"><strong>' . ECLASSF_41 . '</strong></td>
	</tr>
	<tr>
		<td class="forumheader3">{ECLASSF_TANDC}</td>
	</tr>
</table>';
}