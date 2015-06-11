<?php

class ACPList implements Iterator, ArrayAccess, Countable {
	protected $ids;
	protected $items;
	protected $index;
	protected $get_func;
	
	public function __construct($type) {
		$this->index = 0;
		$this->ids = array();
		$this->items = array();
		$this->get_func = "get_{$type}";
	}
	public function add_item($value, $k = null) {
		$k = ($k ? $k : count($this->items));
		if ($value instanceof ACPEditable) {
			if (!empty($value->id)) {
				if (isset($this->ids[$value->id]) && ($this->ids[$value->id] != $k)) {
					ACPManager::error('Could not add value. Given value already exists at another index', 0, ACPManager::E_LEVEL_NOTICE);
					return;
				}
				$this->ids[$value->id] = $k;
			}
			$this->items[$k] = $value;
		}
		elseif ((int)$value) {
			$value = (int)$value;
			if (isset($this->ids[$value]) && ($this->ids[$value] != $k)) {
				ACPManager::error('Could not add value. Given value already exists at another index', 0, ACPManager::E_LEVEL_NOTICE);
				return;
			}
			$this->items[$k] = $value;
			$this->ids[$value] = $k;
		}
		else {
			ACPManager::error('Cannot add item of type ' . gettype($value) . '. Only non-zero integer and ACPEditable are accepted', 0, ACPManager::E_LEVEL_WARNING);
		}
	}
	
	public function get_by_id($id) {
		return (isset($this->ids[(int)$id]) ? $this->items[$this->ids[(int)$id]] : null);
	}
	
	public function get_key_by_id($id) {
		return (isset($this->ids[(int)$id]) ? $this->ids[(int)$id] : null);
	}
	
	public function to_array() {
		return $this->items;
	}
	
	public function get_ids() {
		return array_keys($this->ids);
	}
	
	//Iterator methods
	public function current() {
		return $this->offsetGet($this->index);
	}
	public function key() {
		return $this->index;
	}
	public function next() {
		next($this->items);
		$this->index = key($this->items);
	}
	public function rewind() {
		reset($this->items);
		$this->index = key($this->items);
	}
	public function valid() {
		return isset($this->items[$this->index]);
	}
	
	//ArrayAccess methods
	public function offsetExists($offset) {
		return isset($this->items[(int)$offset]);
	}
	public function offsetGet($offset) {
		$offset = (int)$offset;
		if (!isset($this->items[$offset])) {
			return null;
		}
		if (is_numeric($this->items[$offset])) {
			$get_func = $this->get_func;
			$this->items[$offset] = ACPManager::$get_func($this->items[$offset]);
		}
		return $this->items[$offset];
	}
	public function offsetSet($offset, $value) {
		$this->add_item($value, $offset);
	}
	public function offsetUnset($offset) {
		$offset = (int)$offset;
		if (isset($this->items[$offset])) {
			$vid = (is_numeric($this->items[$offset]) ? $this->items[$offset] : $this->items[$offset]->id);
			if (isset($this->ids[$vid])) {
				unset($this->ids[$vid]);
			}
			unset($this->items[$offset]);
			if ($offset == $this->index) {
				$this->rewind();
			}
		}
	}
	
	//Countable method
	public function count() {
		return count($this->items);
	}
}

?>