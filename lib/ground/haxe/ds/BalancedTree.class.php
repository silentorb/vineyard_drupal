<?php

class haxe_ds_BalancedTree {
	public function __construct() {
		;
	}
	public $root;
	public function set($key, $value) {
		$this->root = $this->setLoop($key, $value, $this->root);
	}
	public function get($key) {
		$node = $this->root;
		while($node !== null) {
			$c = $this->compare($key, $node->key);
			if($c === 0) {
				return $node->value;
			}
			if($c < 0) {
				$node = $node->left;
			} else {
				$node = $node->right;
			}
			unset($c);
		}
		return null;
	}
	public function exists($key) {
		$node = $this->root;
		while($node !== null) {
			$c = $this->compare($key, $node->key);
			if($c === 0) {
				return true;
			} else {
				if($c < 0) {
					$node = $node->left;
				} else {
					$node = $node->right;
				}
			}
			unset($c);
		}
		return false;
	}
	public function setLoop($k, $v, $node) {
		if($node === null) {
			return new haxe_ds_TreeNode(null, $k, $v, null, null);
		}
		$c = $this->compare($k, $node->key);
		if($c === 0) {
			return new haxe_ds_TreeNode($node->left, $k, $v, $node->right, haxe_ds_BalancedTree_0($this, $c, $k, $node, $v));
		} else {
			if($c < 0) {
				$nl = $this->setLoop($k, $v, $node->left);
				return $this->balance($nl, $node->key, $node->value, $node->right);
			} else {
				$nr = $this->setLoop($k, $v, $node->right);
				return $this->balance($node->left, $node->key, $node->value, $nr);
			}
		}
	}
	public function balance($l, $k, $v, $r) {
		$hl = null;
		if($l === null) {
			$hl = 0;
		} else {
			$hl = $l->_height;
		}
		$hr = null;
		if($r === null) {
			$hr = 0;
		} else {
			$hr = $r->_height;
		}
		if($hl > $hr + 2) {
			if(haxe_ds_BalancedTree_1($this, $hl, $hr, $k, $l, $r, $v) >= haxe_ds_BalancedTree_2($this, $hl, $hr, $k, $l, $r, $v)) {
				return new haxe_ds_TreeNode($l->left, $l->key, $l->value, new haxe_ds_TreeNode($l->right, $k, $v, $r, null), null);
			} else {
				return new haxe_ds_TreeNode(new haxe_ds_TreeNode($l->left, $l->key, $l->value, $l->right->left, null), $l->right->key, $l->right->value, new haxe_ds_TreeNode($l->right->right, $k, $v, $r, null), null);
			}
		} else {
			if($hr > $hl + 2) {
				if(haxe_ds_BalancedTree_3($this, $hl, $hr, $k, $l, $r, $v) > haxe_ds_BalancedTree_4($this, $hl, $hr, $k, $l, $r, $v)) {
					return new haxe_ds_TreeNode(new haxe_ds_TreeNode($l, $k, $v, $r->left, null), $r->key, $r->value, $r->right, null);
				} else {
					return new haxe_ds_TreeNode(new haxe_ds_TreeNode($l, $k, $v, $r->left->left, null), $r->left->key, $r->left->value, new haxe_ds_TreeNode($r->left->right, $r->key, $r->value, $r->right, null), null);
				}
			} else {
				return new haxe_ds_TreeNode($l, $k, $v, $r, ((($hl > $hr) ? $hl : $hr)) + 1);
			}
		}
	}
	public function compare($k1, $k2) {
		return Reflect::compare($k1, $k2);
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'haxe.ds.BalancedTree'; }
}
function haxe_ds_BalancedTree_0(&$__hx__this, &$c, &$k, &$node, &$v) {
	if($node === null) {
		return 0;
	} else {
		return $node->_height;
	}
}
function haxe_ds_BalancedTree_1(&$__hx__this, &$hl, &$hr, &$k, &$l, &$r, &$v) {
	{
		$_this = $l->left;
		if($_this === null) {
			return 0;
		} else {
			return $_this->_height;
		}
		unset($_this);
	}
}
function haxe_ds_BalancedTree_2(&$__hx__this, &$hl, &$hr, &$k, &$l, &$r, &$v) {
	{
		$_this1 = $l->right;
		if($_this1 === null) {
			return 0;
		} else {
			return $_this1->_height;
		}
		unset($_this1);
	}
}
function haxe_ds_BalancedTree_3(&$__hx__this, &$hl, &$hr, &$k, &$l, &$r, &$v) {
	{
		$_this2 = $r->right;
		if($_this2 === null) {
			return 0;
		} else {
			return $_this2->_height;
		}
		unset($_this2);
	}
}
function haxe_ds_BalancedTree_4(&$__hx__this, &$hl, &$hr, &$k, &$l, &$r, &$v) {
	{
		$_this3 = $r->left;
		if($_this3 === null) {
			return 0;
		} else {
			return $_this3->_height;
		}
		unset($_this3);
	}
}
