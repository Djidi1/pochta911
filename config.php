<?php
  
define ('DB_HOST','localhost');
//define ('DB_HOST','mysql.bltur.z8.ru');

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	define ('DB_DATABASE','pochta911');
	define ('DB_USER','root');
	define ('DB_PASS','');
} else {
	define ('DB_DATABASE','db_bltur_1');
	define ('DB_USER','dbu_bltur_1');
	define ('DB_PASS','gzccuqFg0B2');
}

define ('DB_USE','mySQL');

define ('DB_TAB_PREF','');
define ('TAB_PREF','');

define ('br','<br />');
define ('rn',"\r\n");
define ('bn',"<br />\r\n");

?>
