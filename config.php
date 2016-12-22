<?php
  
define ('DB_HOST','localhost');
//define ('DB_HOST','mysql.bltur.z8.ru');

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	define ('DB_DATABASE','pochta911');
	define ('DB_USER','root');
	define ('DB_PASS','');
} else {
    define ('DB_DATABASE','pochta911');
    define ('DB_USER','root');
    define ('DB_PASS','');
}

define ('DB_USE','mySQL');

define ('DB_TAB_PREF','');
define ('TAB_PREF','');

define ('TLG_TOKEN','320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c');

define ('br','<br />');
define ('rn',"\r\n");
define ('bn',"<br />\r\n");

