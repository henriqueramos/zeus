<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.display_time_array.php
 * Type:     modifier
 * Name:     display_time_array
 * Purpose:  Display array based in a pattern
 * -------------------------------------------------------------
 */
function smarty_modifier_display_time_array($array)
{
	$finalArray = array();
	
	if(!empty($array['y'])){ $finalArray[] = "{$array['y']} ano(s)";}
	if(!empty($array['m'])){ $finalArray[] = "{$array['m']} meses/m&ecirc;s";}
	if(!empty($array['d'])){ $finalArray[] = "{$array['d']} dia(s)";}
	if(!empty($array['h'])){ $finalArray[] = "{$array['h']} hora(s)";}
	if(!empty($array['i'])){ $finalArray[] = "{$array['i']} minuto(s)";}
	if(!empty($array['s'])){ $finalArray[] = "{$array['s']} segundo(s)";}
	
	return implode(",", $finalArray);;
}
?>