<?php
    
/*
Plugin Name: Check for HfTL IP Range
Plugin URI: http://stura.hftl.de
Description: Prüft ob die Aufrufer IP in einer Range der HfTL-Adressen liegt. Ist dies nicht der Fall löscht das Plugin den Content und zeigt eine Fehlermeldung an. Einfach [check-ip-ranges] in eine Seite einbinden.
Version: 1.0
Author: Tilmann Bach
Author URI: http://laufwerkc.de
*/

    /* Konfiguration */

    $ranges = array("195.145.74.0/24", "195.145.75.0/24", "212.184.75.0/24");
    
    $errormsg = '<p>Diese Seite darf leider nur aus dem Hochschulnetz der HfTL geöffnet werden.</p>';

    /* Konfiguration-Ende */

    function check_for_ranges($content) 
    {
    if(strstr($content, '[check-ip-ranges]') != false)
	{
		if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

	      if(!isInAnyRange($client_ip))
	      {
	        return $errormsg; // return error message instead of original content
	      }
	      else
	      {
	        return str_replace('[check-ip-ranges]', '' , $content);
	      }
	}
	else
	{
		return $content;
	}
    }

    add_filter('the_content', 'check_for_ranges', 30);
    
    function isInAnyRange($checkip)
    {
	global $ranges;
	foreach($ranges as $range)
	{
		if(isInRange($checkip, $range))
			return true;
	}
	return false;
    }
    
    function isInRange($checkip, $range)
    {
      @list($ip, $len) = explode('/', $range);

      if (($min = ip2long($ip)) !== false && !is_null($len)) {
        $clong = ip2long($checkip);
        $max = ($min | (1<<(32-$len))-1);
        if ($clong > $min && $clong < $max) {
          return true;
        } else {
          return false;
        }
      }
    }

?>
