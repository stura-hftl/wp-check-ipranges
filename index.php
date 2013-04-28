<?php
    
/*
Plugin Name: Check for HfTL IP Range
Plugin URI: http://stura.hftl.de
Description: Prüft ob die Aufrufer IP in einer Range der HfTL-Adressen liegt. Ist dies nicht der Fall löscht das Plugin den Content und zeigt eine Fehlermeldung an. Einfach [checkhftlip] in eine Seite einbinden.
Version: 1.0
Author: Tilmann Bach
Author URI: http://laufwerkc.de
*/

    /* Konfiguration */

    $ranges = array(
      "195.145.74.0/24",
      "195.145.75.0/24"
    );
    
    $errormsg = "Diese Seite darf leider nur aus dem Hochschulnetz der HfTL geöffnet werden.";

    /* Konfiguration-Ende */    

    add_filter('the_content', check_for_hftl_ip, 30);

    function check_for_hftl_ip($content) 
    {
      if(!isInAnyRange())
      {
        return str_replace('[checkhftlip]', $errormsg , $content);
      }
      else
      {
        return str_replace('[checkhftlip]', '' , $content);
      }
    }
    
    function isInAnyRange()
    {
      foreach($ranges as $range)
      {
        if(isInRange($range))
          return true;
      }
      return false;
    }
    
    function isInRange($range)
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
