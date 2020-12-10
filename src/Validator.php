<?php

/**
 * Model object validator
 * @package iqomp/model
 * @version 1.0.0
 */

namespace Iqomp\Model;

class Validator
{
    public static function unique($value, $options): ?array
    {
        if (is_null($value)) {
            return null;
        }

        $model  = $options['model'];
        $mfield = $options['field'];
        // $mself  = $options->self ?? null;
        $mwhere = $options['where'] ?? null;

        $cond = [$mfield => $value];
        if ($mwhere) {
            $cond = array_replace($mwhere, $cond);
        }

        $row = $model::getOne($cond);
        if (!$row) {
            return null;
        }

        return ['14.0'];

        // TODO
        // aware self data to skip unique test

        // if (!$mself) {
            // return ['14.0'];
        // }

        // return null;
        // $obj = \Mim::$app;
        // $mself_serv = explode('.', $mself->service);
        // foreach($mself_serv as $prop){
        //     $obj = $obj->$prop ?? null;
        //     if(is_null($obj))
        //         break;
        // }

        // $row_val = $row->{$mself->field};

        // if($row_val == $obj)
        //     return null;
        // return ['14.0'];
    }

    public static function exists($value, $options): ?array
    {
        if (is_null($value)) {
            return null;
        }

        if (!$value) {
            return null;
        }

        $model  = $options['model'];
        $mfield = $options['field'];
        $mwhere = $options['where'] ?? null;

        $cond = [$mfield => $value];
        if ($mwhere) {
            $cond = array_replace($mwhere, $cond);
        }

        $row = $model::getOne($cond);
        if ($row) {
            return null;
        }

        return ['19.0'];
    }

    public static function existsList($value, $options): ?array
    {
        if (is_null($value)) {
            return null;
        }

        if (!$value) {
            return null;
        }

        $value = (array)$value;

        $model  = $options['model'];
        $mfield = $options['field'];
        $mwhere = $options['where'] ?? null;

        $cond = [$mfield => $value];
        if ($mwhere) {
            $cond = array_replace($mwhere, $cond);
        }

        $rows = $model::get($cond);
        if (!$rows) {
            return ['20.0'];
        }

        $values = array_column($rows, $mfield);
        foreach ($value as $val) {
            if (!in_array($val, $values)) {
                return ['20.0'];
            }
        }

        return null;
    }
}
