<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.active_disable.php
 * Type:     modifier
 * Name:     active_disable
 * Purpose:  Conditional statement, if true, return active, if false, return disable.
 * -------------------------------------------------------------
 */
function smarty_modifier_active_disable($string)
{
    return ($string==true)?"active":"disable";
}
?>