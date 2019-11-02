<?php

namespace Kitablog\Lib;
use Illuminate\Http\Request;

class DatatablesLib{
	public static function convertColumnName($name){
		if(strpos($name, '\func')!==false){
			return str_replace('\func', '', $name);
		}else{
			return '`'.implode('`.`',explode('.', $name)).'`';
		}
	}
}
?>