<?php
namespace Client;

class String {

	public $sql = null;
	public $select = [];
	public $from = [];
	public $where = [];
	public $order = [];
	public $skip = [];
	public $limit = [];
	public $errors = [];

	public $basic = [
				'SELECT' => 'SELECT',
				'FROM' => 'FROM',
				'WHERE' => 'WHERE',
				'ORDER BY' => 'ORDER BY',
				'SKIP' => 'SKIP',
				'LIMIT' => 'LIMIT'
			];

	public $conditions = [
				'=',
				'>',
				'<',
				'<>',
				'>=',
				'<='
	];

	public $order_by = [
				'ASC',
				'DESC'
	];

	public function checkString($sql) {
		if(!empty($sql)) {
			$this->sql = trim($sql);
			return true;
		} else {
			$this->errors[] = 'Empty SQL';
			return false;
		}
	}

	public static function noErrors($errors) {
		return (sizeof($errors) == 0);
	}

	public function checkSringStructure() {
		if($this->sql != null) {

			$split_by = $this->basic;

			foreach ($split_by as $key => $value) {
				if(substr_count($this->sql, $value) > 1) {
					$this->errors[] = 'Invalid SQL';
					return false;
				}
			}

			$select_pos = strpos($this->sql, $this->basic['SELECT']);
			$from_pos = strpos($this->sql, $this->basic['FROM']);
			$where_pos = strpos($this->sql, $this->basic['WHERE']);
			$order_pos = strpos($this->sql, $this->basic['ORDER BY']);
			$skip_pos = strpos($this->sql, $this->basic['SKIP']);
			$limit_pos = strpos($this->sql, $this->basic['LIMIT']);

			if($select_pos === false || $from_pos === false || $select_pos > 0 || $select_pos > $from_pos) {
				$this->errors[] = 'Invalid SQL';
				return false;
			}

			if($where_pos === false) {
				unset($split_by['WHERE']);
			}
			if($order_pos === false) {
				unset($split_by['ORDER BY']);
			}
			if($skip_pos === false) {
				unset($split_by['SKIP']);
			}
			if($limit_pos === false) {
				unset($split_by['LIMIT']);
			}

			$split_by_str = implode('|', $split_by);
			$chunks = preg_split('/('.$split_by_str.')/', $this->sql);
			array_shift($chunks);
			$result = array_combine($split_by, $chunks);

			foreach ($result as $key => $value) {
				if(empty(trim($value))) {
					$this->errors[] = 'Empty values';
					return false;
				} else {
					if(self::noErrors($this->errors)) {
						$this->checkPartStructure(trim($key),trim($value));
					}
				}
			}
		} else {
			return false;
		}
	}

	public function checkPartStructure($block, $str) {
		switch ($block) {
			case 'SELECT':
				if(strlen($str) > 0) {
					$str = str_replace(' ', '', $str);
					$select = explode(',', $str);
					$this->select = array_filter($select);
				} else {
					$this->errors[] = 'There are nothing to select';
				}
				break;

			case 'FROM':
				if(strlen($str) > 0) {
					$this->from = [$str];
				} else {
					$this->errors[] = 'There are no table name';
				}
				break;

			case 'WHERE':
				if(strlen($str) > 0) {
					$from_and = explode('AND', $str);
					$from_or = [];
					foreach ($from_and as $key => $value) {
						$or = explode('OR', $value);
						$or_exploded = [];
						foreach ($or as $key => $value) {
							$or_exploded[] = explode(' ', trim($value));
						}
						$from_or[] = $or_exploded;
					}

					$condition = [];
					foreach ($from_or as $key => $and) {
						foreach ($and as $key => $or) {
							if(sizeof($or) != 3 || !in_array($or[1], $this->conditions)) {
								$this->errors[] = 'Not allowed WHERE condition';
								break;
							}
						}
					}

					$this->where = $from_or;
				} else {
					$this->errors[] = 'There are no WHERE condition';
				}
				break;

			case 'ORDER BY':
				if(strlen($str) > 0) {
					$order = explode(' ', $str);
					if(sizeof($order) != 2 || !isset($order[1]) || !in_array(trim($order[1]), $this->order_by)) {
						$this->errors[] = 'Not allowed ORDER condition';
					}
					$this->order = $order;
				} else {
					$this->errors[] = 'There are no ORDER condition';
				}
				break;

			case 'SKIP':
				if(strlen($str) > 0 && is_numeric($str)) {
					$this->skip = [$str];
				} else {
					$this->errors[] = 'Not allowed SKIP value';
				}
				break;

			case 'LIMIT':
				if(strlen($str) > 0 && is_numeric($str)) {
					$this->limit = [$str];
				} else {
					$this->errors[] = 'Not allowed LIMIT value';
				}
				break;

			default:
				return false;
				break;
		}
	}
}