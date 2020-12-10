<?php

/**
 * Model formatter plugin
 * @package iqomp/model
 * @version 1.0.0
 */

namespace Iqomp\Model;

use Iqomp\Formatter\Formatter as _Formatter;
use Iqomp\Formatter\Object\Std;

class Formatter
{
    protected static function propAsKey(array $array, string $prop): array
    {
        $res = [];
        foreach ($array as $arr) {
            $key = is_array($arr) ? $arr[$prop] : $arr->$prop;
            if (is_object($key)) {
                $key = (string)$key;
            }
            $res[$key] = $arr;
        }

        return $res;
    }

    protected static function asArray(array $values): array
    {
        $result = [];
        foreach ($values as $val) {
            $result[$val] = [];
        }

        return $result;
    }

    protected static function asId(array $values): array
    {
        $result = [];
        foreach ($values as $val) {
            $result[$val] = new Std($val);
        }

        return $result;
    }

    protected static function asNull(array $values): array
    {
        $result = [];
        foreach ($values as $val) {
            $result[$val] = null;
        }
        return $result;
    }

    protected static function procValues(array $ids, array $fmt, $opts): array
    {
        $model       = $fmt['model'];
        $model_name  = $model['name'];
        $model_field = $model['field'] ?? 'id';

        $where = [
            $model_field => $ids
        ];

        if (is_array($opts) && isset($opts['_where'])) {
            $where = array_replace($where, $opts['_where']);
            unset($opts['_where']);
        }
        $rows = $model_name::get($where);

        if (!$rows) {
            return [];
        }

        $as_key = self::propAsKey($rows, $model_field);

        // filter one field only
        if (isset($fmt['field'])) {
            $tmp_as_key = [];
            foreach ($as_key as $id => $object) {
                $fname    = $fmt['field']['name'];
                $ftype    = $fmt['field']['type'] ?? null;
                $used_val = $object->$fname;

                if ($ftype) {
                    $used_val = _Formatter::typeApply(
                        $ftype,
                        $used_val,
                        $fname,
                        $object,
                        [],
                        null
                    );
                }

                $tmp_as_key[$id] = $used_val;
            }

            $as_key = $tmp_as_key;
        } elseif (isset($fmt['fields'])) {
            $tmp_as_key = [];
            foreach ($as_key as $id => $object) {
                $used_vals = (object)[];
                foreach ($fmt['fields'] as $field) {
                    $fname = $field['name'];
                    $ftype = $field['type'] ?? null;

                    $used_val = $object->$fname;

                    if ($ftype) {
                        $used_val = _Formatter::typeApply(
                            $ftype,
                            $used_val,
                            $fname,
                            $object,
                            [],
                            null
                        );
                    }

                    $used_vals->$fname = $used_val;
                }
                $tmp_as_key[$id] = $used_vals;
            }
            $as_key = $tmp_as_key;
        }

        if (isset($fmt['format']) && !isset($fmt['field'])  && !isset($fmt['fields'])) {
            if (!is_array($opts)) {
                $opts = [];
            }

            $as_key = _Formatter::formatMany(
                $fmt['format'],
                $as_key,
                $opts,
                $model_field
            );
        }

        return $as_key;
    }

    public static function chain(
        array $values,
        string $field,
        array $objects,
        array $format,
        $options
    ): array {
        if (is_null($options)) {
            return self::asArray($values);
        }

        $chain       = $format['chain'];
        $chain_model = $chain['model']['name'];
        $chain_field = $chain['model']['field'] ?? 'id';

        $chain_rows = $chain_model::get([
            $chain_field => $values
        ]);

        if (!$chain_rows) {
            return self::asArray($values);
        }

        $parent_chains = [];
        foreach ($chain_rows as $row) {
            $parent_id = $row->{$chain_field};
            $child_id  = $row->{$chain['identity']};

            if (!isset($parent_chains[$parent_id])) {
                $parent_chains[$parent_id] = [];
            }
            $parent_chains[$parent_id][] = $child_id;
        }

        $child_ids = array_column($chain_rows, $chain['identity']);
        $child_ids = array_values(array_unique($child_ids));

        $children  = self::procValues($child_ids, $format, $options);

        $result    = [];

        foreach ($parent_chains as $parent => $childs) {
            if (!isset($result[$parent])) {
                $result[$parent] = [];
            }

            foreach ($childs as $child) {
                if (isset($children[$child])) {
                    $result[$parent][] = $children[$child];
                }
            }
        }

        return $result;
    }

    public static function multipleObject(
        array $values,
        string $field,
        array $objects,
        array $format,
        $options
    ): array {
        $sep     = $format->separator ?? ',';
        $objs_id = [];
        $val_ids = [];

        foreach ($values as $val) {
            if ($sep === 'json') {
                $vals = json_decode($val);
            } else {
                $vals = explode($sep, $val);
            }
            $objs_id = array_merge($objs_id, $vals);
            $val_ids[$val] = $vals;
        }

        if (!$objs_id) {
            return [];
        }

        $objs_id = array_unique($objs_id);
        $result  = [];

        if (is_null($options)) {
            $objs_id = self::asId($objs_id);
        } else {
            $objs_id = self::procValues($objs_id, $format, $options);
        }

        foreach ($val_ids as $key => $ids) {
            $key_values = [];
            foreach ($ids as $id) {
                if (isset($objs_id[$id])) {
                    $key_values[] = $objs_id[$id];
                }
            }
            $result[$key] = $key_values;
        }

        return $result;
    }

    public static function object(
        array $values,
        string $field,
        array $objects,
        array $format,
        $options
    ): array {
        if (is_null($options)) {
            $values = self::asId($values);
            if (isset($format['model']['type'])) {
                foreach ($values as $index => $val) {
                    $val->id = _Formatter::typeApply(
                        $format['model']['type'],
                        $val->id,
                        'id',
                        $val,
                        (object)[],
                        null
                    );

                    $values[$index] = $val;
                }
            }
            return $values;
        }

        return self::procValues($values, $format, $options);
    }

    public static function objectSwitch(
        array $values,
        string $field,
        array $objects,
        array $format,
        $options
    ): array {
        $case_field = $format['field'];
        $cases      = $format['cases'];

        $map_values = [];
        foreach ($objects as $object) {
            $case_value = $object->{$case_field};
            $obj_value  = $object->$field;
            if (is_null($obj_value)) {
                continue;
            }

            if (is_object($case_value)) {
                $case_value = $case_value->value ?? (string)$case_value;
            }

            if (!isset($map_values[$case_value])) {
                $map_values[$case_value] = (object)[
                    'case'   => $case_value,
                    'values' => [],
                    'result' => []
                ];
            }
            $map_values[$case_value]->values[] = $object->$field;
        }

        foreach ($map_values as $type => &$vals) {
            $vals->values = array_unique($vals->values);

            if (!isset($cases[$type]) || is_null($options)) {
                foreach ($vals->values as $val) {
                    $vals->result[$val] = new Std($val);
                }
                continue;
            }

            $case         = $cases[$type];
            $vals->result = self::procValues($vals->values, $case, $options);
        }
        unset($vals);

        $result = [];

        foreach ($objects as $object) {
            $case_value = $object->{$case_field};
            $obj_value  = $object->$field;
            $obj_id     = $object->id;
            if (is_null($obj_value)) {
                continue;
            }

            if (is_object($case_value)) {
                $case_value = $case_value->value ?? (string)$case_value;
            }

            $case_result = $map_values[$case_value]->result;
            $result[$obj_id] = $case_result[$obj_value] ?? new Std($obj_value);
        }

        return $result;
    }

    public static function partial(
        array $values,
        string $field,
        array $objects,
        array $format,
        $options
    ): array {
        if (is_null($options)) {
            return self::asNull($values);
        }

        $result = self::procValues($values, $format, $options);
        return $result;
    }
}
