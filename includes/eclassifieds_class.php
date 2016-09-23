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

include_lan(e_PLUGIN . 'e_classifieds/languages/' . e_LANGUAGE . '.php');

class eclassifieds
{
    var $eclassf_admin = false; // is user an admin
    var $eclassf_creator = false; // permitted to create recipes
    var $eclassf_reader = false; // allowed to read recipes
    var $eclassf_auto = false; // allowed to auto approve
    function __construct()
    {
        global $ECLASSF_PREF;
        $this->load_prefs();
        $this->eclassf_admin = check_class($ECLASSF_PREF['eclassf_admin']);
        $this->eclassf_creator = $this->eclassf_admin || check_class($ECLASSF_PREF['eclassf_create']);
        $this->eclassf_reader = check_class($ECLASSF_PREF['eclassf_read']);
        $this->eclassf_auto = check_class($ECLASSF_PREF['eclassf_auto']);
    }
    // ********************************************************************************************
    // *
    // * classifieds load and Save prefs
    // *
    // ********************************************************************************************
    function getdefaultprefs()
    {
        global $ECLASSF_PREF, $e107, $pref, $sql;
            // create new default prefs
            $ECLASSF_PREF =  array('eclassf_email' => 'youremail@yourdomain.com',
                'eclassf_approval' => 1,
                'eclassf_valid' => 90,
                'eclassf_read' => 0,
                'eclassf_create' => 0,
                'eclassf_admin' => '253',
                'eclassf_useremail' => 1,
                'eclassf_terms' => 'Only suitable material will be allowed. Adverts will be checked. This site is not responsible for the goods or services',
                'eclassf_perpage' => '10',
                'eclassf_create' => '0',
                'eclassf_picw' => '100',
                'eclassf_pich' => '100',
                'eclassf_metad' => 'Father Barry"s classifieds plugin for the e107 CMS system',
                'eclassf_metak' => 'father barry,barry keal,e107 plugin,e107 plugins,bazzer',
                'eclassf_icons' => '1',
                'eclassf_thumbs' => '1',
                'eclassf_thumbheight' => '50',
                'eclassf_counter' => 'text',
                'eclassf_userating' => 1,
                'eclassf_dformat' => 'd-m-Y',
                'eclassf_subdrop' => 1,
                "eclassf_force_main_cat" => 0,
                "eclassf_force_sub_cat" => 0,
                "eclassf_pictype" => 0,
                "eclassf_useseo"=>0,
				'eclassf_notifymethod'=>2,
				'eclassf_leadz'=>3,
				'eclassf_maxpic'=>100,
				'eclassf_watermark'=>'(C) Fr Barry',
                );

    }
    function save_prefs()
    {
        global $sql, $eArrayStorage, $ECLASSF_PREF;
        // save preferences to database
        if (!is_object($sql))
        {
            $sql = new db;
        }

        $tmp = $eArrayStorage->WriteArray($ECLASSF_PREF);
        $sql->db_Update('core', 'e107_value="' . $tmp . '" where e107_name="classifieds"', false);
        return ;
    }
    function load_prefs()
    {
        global $sql, $eArrayStorage, $ECLASSF_PREF;
        // get preferences from database
        if (!is_object($sql))
        {
            $sql = new db;
        }
        $num_rows = $sql->db_Select('core', '*', 'where e107_name="classifieds"','nowhere',false);
        $row = $sql->db_Fetch();

        if (empty($row['e107_value']))
        {
            // insert default preferences if none exist
            $this->getDefaultPrefs();
            $tmp = $eArrayStorage->WriteArray($ECLASSF_PREF);
            $sql->db_Insert('core', '"classifieds", "' . $tmp . '" ',false);
            $sql->db_Select('core', '*', 'where e107_name="classifieds" ','nowhere',false);
        }
        else
        {
            $ECLASSF_PREF = $eArrayStorage->ReadArray($row['e107_value']);
        }
        return;
    }
    function clear_cache()
    {
        global $e107cache;
        #$e107cache->clear('nq_recipetop_menu');
        #$e107cache->clear('nq_recipe_menu');
    }
	/*
	function gen_pic($picture = '', $title = '', $height = 0, $width = 0, $lightbox = false)
	{
		global $eclassf_PREF;
		// if (empty($this->recipe_watermark)) {
		// // force false if there is no watermark text
		// $watermark = false;
		// }
		$title = htmlspecialchars($title);

		$eclassf_picloc = e_PLUGIN . "recipe_menu/images/pictures/" . $picture;
		$height_style = '';
		$width_style = '';

		if ($height > 0) {
			$height_style = "height:{$height}px;";
		}

		if ($width > 0) {
			$width_style = "width:{$width}px;";
		}
		if (!empty($picture) && is_readable($eclassf_picloc)) {
			$rel = 'lightbox.rcp_gallery';

			if ($lightbox) {
				// with lightbox
				return "<a href='" . htmlspecialchars(e_PLUGIN . "recipe_menu/image.php?rcp_picture={$picture}") . "' rel='" . $rel . "' title='" . $title . "' ><img src='" . htmlspecialchars(e_PLUGIN . "recipe_menu/image.php?rcp_picture={$picture}&rcp_height=$height&rcp_width=$width") . "' style='border:0; " . $height_style . " " . $width_style . ";' title='" . $title . "' alt='" . $title . "' /></a>";
			} else {
				// without lightbox
				return "<img src='" . htmlspecialchars(e_PLUGIN . "recipe_menu/image.php?&rcp_picture={$picture}&rcp_height=$height&rcp_width=$width") . "' style='border:0; " . $height_style . " " . $width_style . ";' title='" . $title . "' alt='" . $title . "' />";
			}
		} else {
			// picture not found
			return false;
		}
	}
	*/
	function regen_htaccess($onoff)
	{
		global $PLUGINS_DIRECTORY;
		$hta = '.htaccess';
		$pattern = array("\n", "\r");
		$replace = array("", "");
		// if (is_writable($hta) || !file_exists($hta)) {
		// open the file for reading and get the contents
		$file = file($hta);
		$skip_line = false;
		unset($new_line);
		foreach($file as $line) {
			if (strpos($line, '*** MCLASSIFIEDS REWRITE BEGIN ***') > 0) {
				// we start skipping
				$skip_line = true;
			}

			if (!$skip_line) {
				// print strlen($line) . '<br>';
				$new_line[] = str_replace($pattern, $replace, $line);
			}
			if (strpos($line, '*** MCLASSIFIEDS REWRITE END ***') > 0) {
				$skip_line = false;
			}
		}
		if ($onoff == 'on') {
			// $base_loc = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
			$base_loc = e_HTTP . $PLUGINS_DIRECTORY . 'e_classifieds/';
			$new_line[] = "#*** MCLASSIFIEDS REWRITE BEGIN ***";
			$new_line[] = 'RewriteEngine On';
			$new_line[] = "RewriteBase $base_loc";
			$new_line[] = 'RewriteRule classified-([0-9]*)-([a-zA-Z0-9-]*)-([0-9]*)-([0-9]*)-([0-9]*)\.html(.*)$ classifieds.php?$1.$2.$3.$4.$5 [L]';
			$new_line[] = 'RewriteRule classified.html classifieds.php [L]';
			$new_line[] = '#*** MCLASSIFIEDS REWRITE END ***';
			$outwrite = implode("\n", $new_line);
		} else {
			$outwrite = implode("\n", $new_line);
		}
		$retval = 0;
		if ($fp = fopen('tmp.txt', 'wt')) {
			// we can open the file for reading
			if (fwrite($fp, $outwrite) !== false) {
				fclose($fp);
				// we have written the new data to temp file OK
				if (file_exists('old.htaccess')) {
					// there is an old htaccess file so delete it
					if (!unlink('old.htaccess')) {
						$retval = 2;
					}
				}
				if ($retval == 0) {
					// old one deleted OK so rename the existing to the old one
					if (is_readable('.htaccess') && file_exists('tmp.txt')) {
						// if there is an old .htaccess then rename it
						if (!rename('.htaccess', 'old.htaccess')) {
							$retval = 3;
						}
					}
				}
				if ($retval == 0) {
					// successfully renamed existing htaccess to old.htaccess
					// so rename the temp file to .htaccess
					if (!rename('tmp.txt', '.htaccess')) {
						$retval = 4;
					}
				}
			} else {
				// unable to open temporary file
				$retval = 5;
			}
		} else {
			fclose($fp);
			$retval = 1;
		}
		return $retval;
		// }
	}
	function make_url($eclassf_from = 0, $eclassf_action = '', $eclassf_id = 0, $eclassf_cat = 0, $eclassf_order = 0)
	{
		global $ECLASSF_PREF;
		$eclassf_from=(int)$eclassf_from;
		$eclassf_id=(int)$eclassf_id;
		$eclassf_cat=(int)$eclassf_cat;
		$eclassf_order=(int)$eclassf_order;
		if ($ECLASSF_PREF['eclassf_useseo'] == 1) {
			return e_PLUGIN."e_classifieds/mclassified-{$eclassf_from}-{$eclassf_action}-{$eclassf_id}-{$eclassf_cat}-{$eclassf_order}.html";
		} else {
			return e_PLUGIN."e_classifieds/classifieds.php?{$eclassf_from}.{$eclassf_action}.{$eclassf_id}.{$eclassf_cat}.{$eclassf_order}";
		}
	}
  /*
    function makePic($i_file, $t_ht = 100)
    {
        $o_file = e_PLUGIN . 'e_classifieds/images/classifieds/' . $i_file;
        $resfile = e_PLUGIN . 'e_classifieds/images/classifieds/pic_' . $i_file;
        $image_info = getImageSize($o_file) ; // see EXIF for faster way
        $eclassf_type = '';
        switch ($image_info['mime'])
        {
            case 'image/gif':
                if (imagetypes() &IMG_GIF) // not the same as IMAGETYPE
                    {
                        $o_im = imageCreateFromGIF($o_file) ;
                    $eclassf_type = 'gif';
                }
                else
                {
                    $ermsg = 'GIF images are not supported<br />';
                }
                break;
            case 'image/jpeg':
                if (imagetypes() &IMG_JPG)
                {
                    $o_im = imageCreateFromJPEG($o_file) ;
                    $eclassf_type = 'jpg';
                }
                else
                {
                    $ermsg = 'JPEG images are not supported<br />';
                }
                break;
            case 'image/png':
                if (imagetypes() &IMG_PNG)
                {
                    $o_im = imageCreateFromPNG($o_file) ;
                    $eclassf_type = 'png';
                }
                else
                {
                    $ermsg = 'PNG images are not supported<br />';
                }
                break;
            case 'image/wbmp':
                if (imagetypes() &IMG_WBMP)
                {
                    $o_im = imageCreateFromWBMP($o_file) ;
                    $eclassf_type = 'wbmp';
                }
                else
                {
                    $ermsg = 'WBMP images are not supported<br />';
                }
                break;
            default:
                $ermsg = $image_info['mime'] . ' images are not supported<br />';
                break;
        }

        if (!isset($ermsg))
        {
            $o_wd = imagesx($o_im) ;
            $o_ht = imagesy($o_im) ;
            // thumbnail width = target * original width / original height
            $t_wd = round($o_wd * $t_ht / $o_ht) ;

            $t_im = imageCreateTrueColor($t_wd, $t_ht);

            imageCopyResampled($t_im, $o_im, 0, 0, 0, 0, $t_wd, $t_ht, $o_wd, $o_ht);
            switch ($eclassf_type)
            {
                case 'gif':
                    imagegif($t_im, $resfile);
                    break;
                case 'jpg':
                    imageJPEG($t_im, $resfile);
                    break;
                case 'png':
                    imagepng($t_im, $resfile);
                    break;
                case 'wbmp':
                    imagewbmp($t_im, $resfile);
                    break;
            }

            chmod("./images/classifieds/" . $resfile, 0644);
            imageDestroy($o_im);
            imageDestroy($t_im);
        }
        return isset($ermsg)?false:'pic_' . $i_file;
    }
*/
}