<?php
include 'config.php';
include 'class.lpdo.php';
$db = new lpdo($config['Database']);

$table = 'yourtable';
$condition = array('id' => 1, 'name' => 'yourname', array('id', 'name'));
//get one
$rs = $db->get_one($table, $condition);
print_r($rs);
//get more res
$rs = $db->get_rows($table, $condition);
print_r($rs);

/// --- Update 2018.10

/// *** Search.
/// **** Numeral compare.
$result = $db->get_rows($table, 
	array('code' => 'code content', 
		'count' => array(100, '>=', 'OR', array('point' => array(80, '>=')))));

/// **** 'OR' condition.
$result = $db->get_rows($table, 
		array('thisfield' => array(0, '=', 'OR', array('thatfield' => 1))));

/// **** 'OR' with Numeral compare.
$result = $db->get_rows($table, 
		array('thisfield' => array(0, '=', 'OR', array('thatfield' => array(1, '>=')))));

// **** Use 'IN' condition:
// select * from referrals where id IN (101,203,300)
$result = $db->get_rows($table, 
		array('id' => array(array(101, 203, 300), 'IN')));

// select * from referrals where ( id IN (101,203,300) AND thatfield = 1 ) 
$result = $db->get_rows($table, 
		array('id' => array(array(101, 203, 300), 'IN', 'AND', array('thatfield' => 1))));

/// **** Insert; return number of effect row.
$db->insert($table, 
	array('name' => 'codesample',
		'age' => 12,
		'point' => 77));


/// *** Delete; return number of effect row.
$result = $db->delete($table, 'id = 1');
$result = $db->delete($table, array('id' => 2));


/// *** Update; return number of effect row.
$result = $db->update($table, array('name' => 'studentsName'), 'id = 3');
$result = $db->update($table, array('name' => 'studentsName'), 'id > 5 OR id <= 4');

/// *** Save; return number of effect row.
/// *** NOTE: 
/// *** 	1) Update specified data 
/// *** 	2) Or insert data, if no data existing.
$result = $db->save($table, array('name' => 'author'), array('id' => 9));
$result = $db->save($table, array('name' => 'real author'), 'id > 1');

var_dump($result);


?>