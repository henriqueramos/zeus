<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.yes_no.php
 * Type:     modifier
 * Name:     yes_no
 * Purpose:  Conditional statement, if true, return string yes, if false, return string no.
 * -------------------------------------------------------------
 */
function smarty_modifier_yes_no($string)
{
    return ($string==true)?"yes":"no";
}
?>