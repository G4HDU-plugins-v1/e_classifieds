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

global $eclassf_obj, $ECLASSF_PREF,$PLUGINS_DIRECTORY;
if (!is_object($eclassf_obj)) {
    require_once(e_PLUGIN . 'e_classifieds/includes/eclassifieds_class.php');

    $eclassf_obj = new eclassifieds;
}

// ##### e_rss.php ---------------------------------------------
// get all the categories
$feed['name'] = ECLASSF_RSS_1;
$feed['url'] = 'e_classifieds';
$feed['topic_id'] = '';
$feed['path'] = 'e_classifieds';
$feed['text'] = ECLASSF_RSS_2 ;
$feed['class'] = '0';
$feed['limit'] = '9';
$eplug_rss_feed[] = $feed;
// ##### --------------------------------------------------------
// ##### create rss data, return as array $eplug_rss_data -------
$rss = array();
require_once(e_HANDLER . 'userclass_class.php');
if (check_class($ECLASSF_PREF['eclassf_read'])) {
    if ($this->topicid > 0) {
        $eclassf_cat = " and eclassf_category = " . $this->topicid . " ";
    }
    // get unexpired adds which are approved and are visible to this class
    $eclassf_args = "
		select a.eclassf_id,a.eclassf_name,a.mlcassf_posted,a.eclassf_expires,a.eclassf_price,a.eclassf_user,a.eclassf_details,a.eclassf_id,c.eclassf_catname,s.eclassf_subname,s.eclassf_categoryid,s.eclassf_subid from #eclassf_ads as a
		left join #eclassf_subcats as s
		on s.eclassf_subid = a.eclassf_category
		left join #eclassf_cats as c
		on s.eclassf_categoryid = c.eclassf_catid
		where find_in_set(eclassf_catclass,'" . USERCLASS_LIST . "') $eclassf_cat";
    if ($ECLASSF_PREF['eclassf_approval'] == 1) {
        $eclassf_args .= "
		and eclassf_approved > 0";
    }
    if ($ECLASSF_PREF['eclassf_valid'] > 0) {
        $eclassf_args .= "
		and (eclassf_expires>'" . time() . "' or eclassf_expires=0)";
    }
    $eclassf_args .= "
		order by mlcassf_posted desc
		LIMIT 0," . $this->limit;

    if ($items = $sql->db_Select_gen($eclassf_args, false)) {
        $i = 0;
        while ($rowrss = $sql->db_Fetch()) {
            $eclassf_tmp = explode('.', $rowrss['eclassf_user'], 2);
            $rss[$i]['author'] = $tp->toRSS($eclassf_tmp[1]) ;
            $rss[$i]['author_email'] = '';
            $url = $eclassf_obj->make_url(0, 'item', $rowrss['eclassf_categoryid'], $rowrss['eclassf_subid'] , $rowrss['eclassf_id']);
          #   print basename($url);
        	$correctedurl=basename($url);
            $rss[$i]['link'] = SITEURL . $PLUGINS_DIRECTORY . $correctedurl ;
            $rss[$i]['linkid'] = $rowrss['eclassf_id'];
            $rss[$i]['title'] = $tp->toRSS($rowrss['eclassf_name']);
            $rss[$i]['description'] = $tp->toRSS($rowrss['eclassf_details']);

            $rss[$i]['category_name'] = 'dd';
            $rss[$i]['category_link'] = '';

            $rss[$i]['datestamp'] = $rowrss['mlcassf_posted'];
            $rss[$i]['enc_url'] = '';
            $rss[$i]['enc_leng'] = '';
            $rss[$i]['enc_type'] = '';
            $i++;
        }
    } else {
        $rss[$i]['author'] = substr($rowrss['eclassf_user'], strpos($rowrss['eclassf_user'], '.') + 1);
        $rss[$i]['author_email'] = '';
        $url = $eclassf_obj->make_url(0, '', 0, 0 , 0);
        // print basename($url);
        $rss[$i]['link'] = SITEURL . $PLUGINS_DIRECTORY . $url;
        $rss[$i]['linkid'] = '';
        $rss[$i]['title'] = ECLASSF_RSS_5;
        $rss[$i]['description'] = ECLASSF_RSS_6;
        $rss[$i]['category_name'] = '';
        $rss[$i]['category_link'] = '';
        $rss[$i]['datestamp'] = '';
        $rss[$i]['enc_url'] = '';
        $rss[$i]['enc_leng'] = '';
        $rss[$i]['enc_type'] = '';
    }
} else {
    $rss[$i]['author'] = '';
    $rss[$i]['author_email'] = '';
    $url = $eclassf_obj->make_url(0, '', 0, 0 , 0);
    // print basename($url);
    $rss[$i]['link'] = SITEURL . $PLUGINS_DIRECTORY . $url;
    $rss[$i]['linkid'] = '';
    $rss[$i]['title'] = ECLASSF_RSS_3;
    $rss[$i]['description'] = ECLASSF_RSS_4;
    $rss[$i]['category_name'] = '';
    $rss[$i]['category_link'] = '';
    $rss[$i]['datestamp'] = '';
    $rss[$i]['enc_url'] = '';
    $rss[$i]['enc_leng'] = '';
    $rss[$i]['enc_type'] = '';
}

$eplug_rss_data[] = $rss;

?>