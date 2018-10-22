<?php
/**
 * 
 * @Author : Poplax [Email:linjiang9999@gmail.com]; 
 * @Date : Fri Jun 03 10:17:17 2011;
 * @Filename class.lpdo.php;
 */

/**
 * class lpdo PDO
 * one table support only
 */
class lpdo extends PDO
{
	public $sql = '';
	public $tail = '';
	private $charset = 'UTF8';
	private $options;

	const DEFAULT_LOGIC = 'AND';

	/**
	 * 
	 * @Function : __construct;
	 * @Param  $ : $options Array DB config ;
	 * @Return Void ;
	 */
	public function __construct($options)
	{
		$this->options = $options;
		$dsn = $this->createdsn($options);
		$attrs = empty($options['charset']) ? array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->charset) : array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $options['charset']);
		try
		{
			parent::__construct($dsn, $options['username'], $options['password'], $attrs);
		}
		catch (PDOException $e)
		{
			echo 'Connection failed: ' . $e->getMessage();
		}
	}
	
	/**
	 * 
	 * @Function : createdsn;
	 * @Param  $ : $options Array;
	 * @Return String ;
	 */
	private function createdsn($options)
	{
		return $options['dbtype'] . ':host=' . $options['host'] . ';dbname=' . $options['dbname'] . ';port=' . $options['port'];
	}

	/**
	 * 
	 * @Function : get_fields;
	 * @Param  $ : $data Array;
	 * @Return String ;
	 */
	private function get_fields($data)
	{
		$fields = array();
		if (is_int(key($data)))
		{
			$fields = '`' . implode('`,`', $data) . '`';
		}
		else if (!empty($data))
		{
			$fields = '`' . implode('`,`', array_keys($data)) . '`';
		}
		else
		{
			$fields = '*';
		}
		return $fields;
	}

	/**
	 * 
	 * @Function : get_condition;
	 * @Param  $ : $condition Array, $oper String, $logc String;
	 * @Return String ;
	 */
	private function get_condition($condition)
	{
		$cdts = '';
		$logc = '';
		$oper = '=';

		if (empty($condition))
		{
			return $cdts = '';
		}
		else if (is_array($condition))
		{
			$_cdta = array();
			foreach($condition as $k => $v)
			{
				if (!is_array($v))
				{
					if (is_string($v))
					{
						$v = '\'' . $v . '\'';
					}

					$logc = count($_cdta) == 0 ? '' : ' ' . self::DEFAULT_LOGIC;
					$_cdta[] = $logc . ' `' . $k . '` ' . $oper . ' ' . $v . ' ';
				}
				else if (is_array($v))
				{
					$logc = empty($v[2]) ? self::DEFAULT_LOGIC : $v[2];
					$sub_condition = trim($this->split_condition($k, $v));
					$where = substr($sub_condition, strlen($logc));
					$logc = count($_cdta) == 0 ? '' : $logc;

					$_cdta[] = " $logc (" . $where . ")";
				}
			}
			$cdts .= implode('', $_cdta);
		}
		else if (is_string($condition))
		{
			$cdts = $condition;
		}
		return $cdts;
	}

	/**
	 * 
	 * @Function : split_condition;
	 * @Param  $ : $field String, $cdt Array;
	 * @Return String ;
	 */
	private function split_condition($field, $cdt)
	{
		if (empty($cdt)) 
		{
			return '';
		}
		else if (is_string($cdt))
		{
			return self::DEFAULT_LOGIC . " `$field` = '{$cdt}' ";
		}
		else if (is_numeric($cdt))
		{
			return self::DEFAULT_LOGIC . " `$field` = {$cdt} ";
		}

		$cdts = '';
		$cdta = array();

		$val =  empty($cdt[0]) && is_string($cdt[0]) ? '' : $cdt[0];
		$oper = empty($cdt[1]) ? '=' : $cdt[1];
		$logc = empty($cdt[2]) ? 'AND' : $cdt[2];
		$options = empty($cdt[3]) ? NULL : $cdt[3];

		if (!is_array($val))
		{
			$val = is_string($val) ? "'$val'" : $val;
		}
		else if (is_array($val) || strtoupper(trim($oper)) == 'IN')
		{
			$val = '(' . implode(',', $val) . ')';
		}

		$cdta[] = "$logc `$field` $oper {$val} ";

		if (!empty($options) 
			&& is_array($options) 
			&& count($options) == 1) 
		{
			$f = '';
			$opt = array();
			foreach ($options as $key => $value) {
				$f = $key;
				$opt = $value;
				break;
			}
			$cdta[] = $this->split_condition($f, $opt);
		}

		$cdts = implode('', $cdta);
		return $cdts;
	}

	/**
	 * 
	 * @Function : get_fields_datas;
	 * @Param  $ : $data Array;
	 * @Return Array ;
	 */
	private function get_fields_datas($data)
	{
		$arrf = $arrd = array();
		foreach($data as $f => $d)
		{
			$arrf[] = '`' . $f . '`';
			$arrd[] = is_string($d) ? '\'' . $d . '\'' : $d;
		}
		$res = array(implode(',', $arrf), implode(',', $arrd));
		return $res;
	}

	/**
	 * 
	 * @Function : get_rows;
	 * @Param  $ : $table String, $getRes Boolean, $condition Array, $column Array;
	 * @Return Array |Object;
	 */
	public function get_rows($table, $condition = array(), $getRes = false, $column = array())
	{
		$fields = $this->get_fields($column);
		$cdts = $this->get_condition($condition);
		$where = empty($condition) ? '' : ' WHERE ' . $cdts;
		$this->sql = 'SELECT ' . $fields . ' FROM ' . $table . $where;
		echo $this->sql . "\n";
		try
		{
			$this->sql .= $this->tail;
			$rs = parent::query($this->sql);
		}
		catch(PDOException $e)
		{
			trigger_error("get_rows: ", E_USER_ERROR);
			echo $e->getMessage() . "<br/>\n";
		}
		$rs = $getRes ? $rs : $rs->fetchAll(parent::FETCH_ASSOC);
		return $rs;
	}

	/**
	 * 
	 * @Function : get_all;
	 * @Param  $ : $table String, $condition Array, $getRes Boolean;
	 * @Return Array |Object;
	 */
	public function get_all($table, $getRes = false, $condition = array())
	{
		return $this->get_rows($table, $condition, $getRes);
	}

	/**
	 * 
	 * @Function : get_one;
	 * @Param  $ : $table String, $condition Array, $getRes Boolean, $column Array;
	 * @Return Array ;
	 */
	public function get_one($table, $condition = array(), $column = array())
	{
		$rs = $this->get_rows($table, $condition, true, $column);
		$rs = $rs ? $rs->fetch(parent::FETCH_ASSOC) : $rs;
		return $rs;
	}

	/**
	 * 
	 * @Function : insert;
	 * @Param  $ : $table String, $data Array;
	 * @Return Int ;
	 */
	public function insert($table, $data)
	{
		list($strf, $strd) = $this->get_fields_datas($data);
		$this->sql = 'INSERT INTO `' . $table . '` (' . $strf . ') VALUES (' . $strd . '); ';
		return $this->exec($this->sql, __METHOD__);
	}

	/**
	 * 
	 * @Function : update;
	 * @Param  $ : $table String, $data Array, $condition Array;
	 * @Return Int ;
	 */
	public function update($table, $data, $condition)
	{
		$cdt = $this->get_condition($condition);
		$arrd = array();
		foreach($data as $f => $d)
		{
			$arrd[] = "`$f` = '$d'";
		}
		$strd = implode(',', $arrd);
		$this->sql = 'UPDATE `' . $table . '` SET ' . $strd . ' WHERE ' . $cdt;
		return $this->exec($this->sql, __METHOD__);
	}

	/**
	 * 
	 * @Function : save;
	 * @Param  $ : $table String, $data Array, $condition Array;
	 * @Return Int ;
	 */
	public function save($table, $data, $condition = array())
	{
		$cdt = $this->get_condition($condition);
		list($strf, $strd) = $this->get_fields_datas($data);
		$has1 = $this->get_one($table, $condition);
		if (!$has1)
		{
			$enum = $this->insert($table, $data);
		}
		else
		{
			$enum = $this->update($table, $data, $condition);
		}
		return $enum;
	}

	/**
	 * 
	 * @Function : delete;
	 * @Param  $ : $table String, $condition Array;
	 * @Return Int ;
	 */
	public function delete($table, $condition)
	{
		$cdt = $this->get_condition($condition);
		$this->sql = 'DELETE FROM `' . $table . '` WHERE ' . $cdt;
		return $this->exec($this->sql, __METHOD__);
	}

	/**
	 * 
	 * @Function : exec;
	 * @Param  $ : $sql, $method;
	 * @Return Int ;
	 */
	public function exec($sql, $method = '')
	{
		try
		{
			$this->sql = $sql . $this->tail;
			echo $this->sql . "\n";
			$efnum = parent::exec($this->sql);
		}
		catch(PDOException $e)
		{
			echo 'PDO ' . $method . ' Error: ' . $e->getMessage();
		}
		return intval($efnum);
	}

	/**
	 * 
	 * @Function : setLimit;
	 * @Param  $ : $start, $length;
	 * @Return ;
	 */
	public function set_limit($start = 0, $length = 20)
	{
		$this->tail = ' LIMIT ' . $start . ', ' . $length;
	}

}
?>