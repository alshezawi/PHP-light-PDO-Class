<?php
$config = array();
$config['Database'] = array();
$config['Database']['dbtype'] = 'mysql';
$config['Database']['dbname'] = 'yourdbname';
$config['Database']['host'] = '127.0.0.1'; //localhost if you have got some error, try "localhost", don't forget control port!
$config['Database']['port'] = 3306;
$config['Database']['username'] = 'yourusername';
$config['Database']['password'] = 'yourpassword';
$config['Database']['charset'] = 'utf8';
$config['Database']['prepare'] = true; // use PDO prepare statement, Default is true.
?>
