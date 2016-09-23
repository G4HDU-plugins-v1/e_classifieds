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
if (!function_exists("eclassifieds_mtemplate"))
{
    function eclassifieds_mtemplate($top_show_author, $top_show_date, $top_show_category,$top_show_info)
    {
        $top_returnval = "{TOPMENU_ITEM}<br />";
        if ($top_show_info)
        {
            $top_returnval .= "{TOPMENU_INFO}<br />";
        }
        if ($top_show_author)
        {
            $top_returnval .= "Posted by {TOPMENU_POSTER}<br />";
        }
        if ($top_show_date)
        {
            $top_returnval .= "Posted on {TOPMENU_DATE}<br />";
        }
        if ($top_show_category)
        {
            $top_returnval .= "Category {TOPMENU_CATEGORY}<br />";
        }
        $top_returnval .= "<br />";
        return $top_returnval;
    }
}
if (!function_exists("eclassifieds_ptemplate"))
{
    function eclassifieds_ptemplate($top_show_author, $top_show_date, $top_show_category,$top_show_info)
    {
        $top_returnval = "{TOPMENU_ITEM}<br />";
        if ($top_show_info)
        {
            $top_returnval .= "{TOPMENU_INFO}<br />";
        }
        if ($top_show_author)
        {
            $top_returnval .= "Posted by {TOPMENU_POSTER}<br />";
        }
        if ($top_show_date)
        {
            $top_returnval .= "Posted on {TOPMENU_DATE}<br />";
        }
        if ($top_show_category)
        {
            $top_returnval .= "Category {TOPMENU_CATEGORY}<br />";
        }
        $top_returnval .= "<br />";
        return $top_returnval;
    }
}

?>