<?PHP

include 'lib/config.php';
include 'lib/class.lpdo.php';

$db = new lpdo($config['Database']);

// Condition AS array
$condition = array('id' => 4, 'name' => 'yourname');

// Select fields
$fields = array('id', 'name');

// Default SQL logical operators is 'AND'
//SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  AND `name` = 'yourname'  LIMIT 0, 1
$result = $db->get_one('yourtable', $condition, $fields);

// Logical operators 'OR'
// SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  OR ( `point` >= 77) OR ( `name` = 'yourname')
$condition = array('id' => 1,
    'point' => array(77, '>=', 'OR'),
    'name' => array('yourname', '=', 'OR'));

// SELECT `id`,`name` FROM yourtable WHERE  `id` = 1  AND `point` = 77 OR ( `name` = 'yourname' AND `code` = 'contenttext') AND `age` = 7
$condition = array('id' => 1,
    'point' => 77,
    'name' => array('yourname', '=', 'OR', array('code' => 'contenttext')),
    'age' => 12);

//SELECT `id`,`name` FROM yourtable WHERE   ( `id` > 4) AND ( `name` =  'yourname'  AND `code` LIKE  'content%') AND `age` = 12
$condition = array('id' => array('4', '>'),
    'name' => array('yourname', '=', 'AND', array(
        'code' => array(
            'content%', 'LIKE', 'AND'))),
    'age' => 12);

// 'LIMIT 0, 1' add to tail.
$result = $db->get_one('yourtable', $condition, $fields);

// SELECT id,name FROM yourtable WHERE  `id` = 1  AND `name` = 'yourname'
// The third bool param: return object if true, or array.
// The fourth array param: select fields.
$result = $db->get_rows('yourtable', $condition, array('id', 'name'), true);

// More example.
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
// SELECT `name` FROM yourtable WHERE  ( `id` IN (101,203,300))
$result = $db->get_rows('yourtable',
    array('id' => array(array(101, 203, 300), 'IN')), array('name'));


// SELECT * FROM yourtable WHERE  ( `id` IN (101,203,300) AND `count` = 1)
$result = $db->get_rows('yourtable',
    array('id' => array(array(101, 203, 300), 'IN', 'AND', array('count' => 1))));


/// **** Insert; return number of effect row.
// INSERT INTO `yourtable` (`name`,`age`,`point`) VALUES ('poplax',22,77);
$result = $db->insert('yourtable',
    array('name' => 'poplax',
        'age' => 22,
        'point' => rand(70, 100)));

/// *** Delete; return number of effect row.
// DELETE FROM `yourtable` WHERE `id` = 1
$result = $db->delete('yourtable', '`id` > 3 AND id < 6');

// DELETE FROM `yourtable` WHERE  `id` = 2
$result = $db->delete('yourtable', array('id' => 2));

/// *** Update; return number of effect row.
// UPDATE `yourtable` SET `name` = 'studentsName' WHERE id = 3
$result = $db->update('yourtable', array('name' => 'studentsName'), 'id = 3');
// UPDATE `yourtable` SET `name` = 'studentsName' WHERE id > 5 OR id <= 10
$result = $db->update('yourtable', array('name' => 'studentsName'),
    array('id' => array(5, '>', '',
        array('id' => array(10, '<=', 'OR')))));

/// *** Save; return number of effect row.
/// *** NOTE:
/// *** 	1) Update specified data
/// *** 	2) Or insert data, if no data existing.
// UPDATE `yourtable` SET `name` = 'author' WHERE  `id` = 100
$result = $db->save('yourtable', array('name' => 'author'), array('id' => 100));

// UPDATE `yourtable` SET `name` = 'real author' WHERE id > 1
$result = $db->save('yourtable', array('name' => 'real author'), 'id > 1');

?>