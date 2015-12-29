<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.about_comparison.php
 * Type:     modifier
 * Name:     about_comparison
 * Purpose:  Conditional statement, return string based into keyword.
 * -------------------------------------------------------------
 */
function smarty_modifier_about_comparison($string)
{
	$finalString = "";
	switch($string){
		case 'not_equal': $finalString="not equal"; break;
		case 'less_than': $finalString="less than"; break;
		case 'greater_than': $finalString="greater than"; break;
		case 'less_than_or_equal_to': $finalString="less than or equal to"; break;
		case 'greater_than_or_equal_to': $finalString="greater than or equal to"; break;
		case 'equal': default: $finalString="equal"; break;
	}
	return $finalString;
}
?>