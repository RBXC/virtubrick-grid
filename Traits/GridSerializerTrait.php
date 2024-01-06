<?php
/*
  Copyright (C) 2024 kylegg. All rights reserved.
*/

namespace Virtubrick\Grid\Traits;

trait GridSerializerTrait
{
	public static function serializeArray(array $array): array
	{
		array_walk($array, fn(&$v) => $v = static::serializeValue($v));
		return $array;
	}
	
	public static function serializeValue(mixed $value): array
	{
		$luaType = match(gettype($value)) {
			'string' => 'LUA_TSTRING',
			'boolean' => 'LUA_TBOOLEAN',
			'double' => 'LUA_TNUMBER',
			'integer' => 'LUA_TNUMBER',
			'array' => 'LUA_TTABLE',
			'object' => 'LUA_TTABLE',
			'NULL' => 'LUA_TNIL'
		};
		
		$result = array_merge(
			['type' => $luaType],
			match($luaType) {
				'LUA_TTABLE' => ['table' => ['LuaValue' => static::serializeArray((array)array_values((array)$value))]],
				'LUA_TBOOLEAN' => ['value' => json_encode($value)],
				'LUA_TNIL' => [],
				default => ['value' => strval($value)]
			}
		);
		
		return $result;
	}
	
	public static function deserializeArray(object|array $array): array
	{
		$result = reset($array);
		if(!property_exists($result, 'LuaValue'))
			return [];
		
		$array = $result->LuaValue;
		if(gettype($array) == 'object')
			$array = [$array];
		
		array_walk($array, fn(&$v) => $v = static::deserializeValue($v));
		return $array;
	}
	
	public static function deserializeValue(object $value): mixed
	{
		switch($value->type)
		{
			case 'LUA_TBOOLEAN':
				return (bool)$value->value;
			case 'LUA_TNUMBER':
				return (double)$value->value;
			case 'LUA_TTABLE':
				if(count((array)$value->table) == 0)
					return [];
				return static::deserializeArray([$value->table]);
			case 'LUA_TNIL':
				return null;
			default:
				return $value->value;
			
		}
	}
}