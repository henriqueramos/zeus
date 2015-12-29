<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.time_ago.php
 * Type:     modifier
 * Name:     time_ago
 * Purpose:  Check how many minutes has passed between two dates
 * -------------------------------------------------------------
 */
function smarty_modifier_time_ago($from, $to)
{
	$fromDate = new DateTime($from);
	$toDate = new DateTime($to);
	$interval = $fromDate->diff($toDate);
	
	return array('y'=>$interval->y, 'm'=>$interval->m, 'd'=>$interval->d, 'h'=>$interval->h, 'i'=>$interval->i, 's'=>$interval->s);
}
?>