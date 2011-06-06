<?php

class ObjectXml
{

	var $childNodes = array ( 
	);

	var $attributes = array ( 
	);

	var $nodeName = '';

	var $text = '';

	function ObjectXml($nodeName = 'root')
	{
		$this->nodeName = $nodeName;
	}

	function &addChild($nodeName, $nodeText = '')
	{
		$node = new ObjectXml ( $nodeName );
		if ($nodeText != '') {
			$node->setText ( $nodeText );
		}
		$this->childNodes [] = &$node;
		return $node;
	}

	function &appendChild(&$node)
	{
		$this->childNodes [] = &$node;
		return $node;
	}

	function addAttribute($name, $value)
	{
		$this->attributes [$name] = $value;
	}

	function asXML($include_version = FALSE)
	{
		$str = ($include_version) ? '<?xml version="1.0"?>' : '';
		if (sizeof ( $this->attributes ) > 0) {
			$attributes = '';
			foreach ( $this->attributes as $key => $value ) {
				$attributes .= ' ' . $key . '="' . $value . '"';
			}
			$str .= "<$this->nodeName $attributes>";
		} else {
			$str .= "<$this->nodeName>";
		}
		foreach ( $this->childNodes as $node ) {
			$str .= $node->asXML ();
		}
		$str .= "$this->text</$this->nodeName>";
		return $str;
	}

	function setText($text)
	{
		$this->text = $text;
	}

}

?>