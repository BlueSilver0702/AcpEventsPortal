<?php

class ACPLang {
	protected $id;
	protected $name;
	protected $code;
	protected $is_default;
	protected $enabled;
	protected $text;
	
	public function __construct($data = null, $loadtext = true) {
		if (is_numeric($data)) {
			$this->load_from_id($data, $loadtext);
		}
		elseif (is_string($data)) {
			$this->load_from_code($data, $loadtext);
		}
		elseif (is_object($data)) {
			$this->load_from_object($data, $loadtext);
		}
	}
	
	public function __invoke($v) {
		return $this->get_text($v);
	}
	
	public function __get($n) {
		$n = strtolower($n);
		return (property_exists($this, $n) ? $this->$n : $this->get_text($n));
	}
	public function __set($n, $v) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			switch ($n) {
				case 'id':
					$this->id = (int)$v;
					break;
				case 'code':
					$this->code = self::to_code($v);
					break;
				case 'is_default':
				case 'enabled':
					$this->$n = (empty($v) || (strtolower($v) == 'no') ? 'No' : 'Yes');
					break;
				case 'text':
					ACPManager::error('Cannot directly modify $text property');
					break;
				default:
					$this->$n = $v;
			}
		}
	}
	
	public function __isset($n) {
		$n = strtolower($n);
		return !empty($this->$n);
	}
	
	public function load_from_id($data, $loadtext = true) {
		$data = ACPManager::lang_by_id($data);
		$this->load_from_object($data, $loadtext);
	}
	
	public function load_from_code($data, $loadtext = true) {
		$data = ACPManager::lang_by_code($data);
		$this->load_from_object($data, $loadtext);
	}
	
	public function load_from_object($data, $loadtext = true) {
		if (!is_object($data)) {
			return;
		}
		$this->__set('id', $data->lang_id);
		$this->__set('name', $data->lang_name);
		$this->__set('code', $data->lang_code);
		$this->__set('is_default', $data->lang_is_default);
		$this->__set('enabled', $data->lang_enabled);
		if ($loadtext) {
			$this->load_text();
		}
	}
	
	public function load_text() {
		if (!empty($this->id)) {
			$text = ACPManager::lang_text_by_lang_id($this->id);
			if (!empty($text)) {
				foreach ($text as $v) {
					$this->text[$v->lobj_code] = new ACPLangText($v);
				}
			}
		}
	}
	
	public function get_text($v) {
		$v = self::to_code($v);
		return (isset($this->text[$v]) ? $this->text[$v] : '');
	}
	
	/*public function save() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot save language with an empty $name property');
		}
		if (empty($this->code)) {
			//return ACPManager::error('Cannot save language with an empty $code property');
			$this->code = self::to_code($this->name, 10);
		}
		
		if (empty($this->id)) {
			return $this->insert();
		}
		return $this->update();
	}*/
	
	public function insert() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot insert language with an empty $name property');
		}
		if (empty($this->code)) {
			$this->code = self::to_code($this->name, 10);
		}
		$this->id = ACPManager::lang_insert($this->name, $this->code, $this->is_default, $this->enabled);
		return (bool)$this->id;
	}
	
	public function update() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot update language with an empty $name property');
		}
		if (empty($this->code)) {
			$this->code = self::to_code($this->name, 10);
		}
		return ACPManager::lang_update($this->id, $this->name, $this->code, $this->is_default, $this->enabled);
	}
	
	public static function to_code($v, $maxlen = 256) {
		$search = array(
			'/\s+/', '/[^a-z0-9\-\_]/i'
		);
		$replace = array(
			'_', ''
		);
		return strtolower(preg_replace($search, $replace, substr($v, 0, $maxlen)));
	}
}

?>