PHP light PDO extend Class
==========

Introduction
----------

A PHP light Class of PDO extend, esay to connect MYSQL and CRUD operate 

Require
----------
PHP 5.1+
php pdo extend.

Change Log
----------

*2018.10.20*

1. Support string condition param.
2. Update README file and add more example.

How To Use
----------

### Create instance

```php
require 'config.php'
require 'class.lpdo.php'
$db = new lpdo($config['Database']);

```

### Example

#### Search (part 1)

```php
// Condition AS array
$condition = array('id' => 1, 'name' => 'yourname');

// Select fields
$fields = array('id', 'name');

// Default SQL logical operators is 'AND'
// SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  AND `name` = 'yourname'
$result = $db->get_one('yourtable', $condition, $fields);

// Logical operators 'OR'
// SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  OR ( `point` >= 80) OR ( `name` = 'yourname') 
$condition = array('id' => 1, 
		'point' => array(80, '>=', 'OR'), 
		'name' => array('yourname', '=', 'OR'));

// SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  AND `point` = 0 OR ( `name` = 'yourname' AND `code` = 'contenttext') AND `age` = 7 
$condition = array('id' => 1, 
		'point' => 0,
		'name' => array('yourname', '=', 'OR', array('code' => 'contenttext')),
		'age' => 7);

// SELECT id,name FROM yourtable WHERE  `id` = 1  AND `point` = 0 OR ( `name` = 'yourname' AND `code` = 'codecontent') AND `age` = 7 
$condition = array('id' => 1, 
		'point' => 0,
		'name' => array('yourname', '=', 'OR', array(
			'code' => array(
				'codecontent', '=', 'AND'))),
		'age' => 7);

// SELECT id,name FROM yourtable WHERE  `id` = 1  AND `name` = 'yourname' 
$result = $db->get_one('yourtable', $condition, $fields);

// SELECT id,name FROM yourtable WHERE  `id` = 1  AND `name` = 'yourname' 
// The third bool param: return object if true, or array.
// The fourth array param: select fields.
$result = $db->get_rows('yourtable', $condition, true, array('id', 'name'));

```

#### Search (part 2)

```php
/// *** Search.
/// **** Numeral compare.

// SELECT * FROM yourtable WHERE  `code` = 'code content' OR ( `count` >= 100 AND `point` >= 80)
$result = $db->get_rows('yourtable', 
	array('code' => 'code content', 
		'count' => array(100, '>=', 'OR', array('point' => array(80, '>=')))));

/// **** 'OR' condition.
// Ignore first logical operators; second logical operators default is 'AND'.
// SELECT * FROM yourtable WHERE ( `point` > 0 AND `count` = 1)
$result = $db->get_rows('yourtable', 
		array('point' => array(0, '>', '', array('count' => 1))));

/// **** 'OR' with Numeral compare.
// SELECT * FROM yourtable WHERE  ( `point` = 0 OR `count` >= 1)
$result = $db->get_rows('yourtable', 
		array('point' => array(0, '=', 'AND', array('count' => array(1, '>=', 'OR')))));

// **** Use 'IN' condition:
// SELECT * FROM yourtable WHERE  ( `id` IN (101,203,300))
$result = $db->get_rows('yourtable', 
		array('id' => array(array(101, 203, 300), 'IN')));

// SELECT * FROM yourtable WHERE  ( `id` IN (101,203,300) AND `count` = 1)
$result = $db->get_rows('yourtable', 
		array('id' => array(array(101, 203, 300), 'IN', 'AND', array('count' => 1))));

```


#### Insert, Update, Delete

```php
/// **** Insert; return number of effect row.
// INSERT INTO `yourtable` (`name`,`age`,`point`) VALUES ('poplax',12,77); 
$db->insert('yourtable', 
	array('name' => 'poplax',
		'age' => 12,
		'point' => 77));


/// *** Delete; return number of effect row.
// DELETE FROM `yourtable` WHERE `id` = 1
$result = $db->delete('yourtable', '`id` = 1');

// DELETE FROM `yourtable` WHERE  `id` = 2 
$result = $db->delete('yourtable', array('id' => 2));

/// *** Update; return number of effect row.
// UPDATE `yourtable` SET `name` = 'studentsName' WHERE id = 3
$result = $db->update('yourtable', array('name' => 'studentsName'), 'id = 3');
// UPDATE `yourtable` SET `name` = 'studentsName' WHERE id > 5 OR id <= 4
$result = $db->update('yourtable', array('name' => 'studentsName'), 'id > 5 OR id <= 4');

/// *** Save; return number of effect row.
/// *** NOTE: 
/// *** 	1) Update specified data 
/// *** 	2) Or insert data, if no data existing.
// UPDATE `yourtable` SET `name` = 'author' WHERE  `id` = 9 
$result = $db->save('yourtable', array('name' => 'author'), array('id' => 9));

// UPDATE `yourtable` SET `name` = 'real author' WHERE id > 1
$result = $db->save('yourtable', array('name' => 'real author'), 'id > 1');


```

NOTE 
----------
Take your own risk, If use 'String' Type as a condition parameter.


*See file: usage.php*

