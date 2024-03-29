<?php

/**
 * Model object builder
 * @package iqomp/model
 * @version 2.2.0
 */

namespace Iqomp\Model;

use Iqomp\Config\Fetcher as Config;

class Model
{
    protected static $models = [];

    protected static function buildModel($model)
    {
        $configs = config('model');
        $conns   = config('databases');

        if (!$configs['drivers']) {
            $msg = 'No model driver installed, please install one';
            throw new DriverNotInstalledException($msg);
        }

        $connections = [
            'read'  => null,
            'write' => null
        ];

        $options = [
            'model'       => $model,
            'table'       => $model::$table,
            'chains'      => $model::$chains ?? [],
            'q_fields'    => $model::$q_fields ?? [],
            'connections' => $connections
        ];

        // chains
        $chains = $configs['chains'] ?? null;
        if ($chains) {
            if (isset($chains[$model])) {
                $model_chain = $chains[$model];
                $opt_chain = $options['chains'];
                $opt_chain = array_replace_recursive($opt_chain, $model_chain);
                $options['chains'] = $opt_chain;
            }
        }

        if (isset($configs['models'][$model])) {
            $connections = $configs['models'][$model];
        } else {
            $connections = [
                'read'  => 'default',
                'write' => 'default'
            ];

            foreach ($configs['models'] as $name => $conn) {
                $regex = preg_quote($name);
                $regex = str_replace('\*', '.+', $regex);

                if (preg_match('!^' . $regex . '$!', $model)) {
                    $connections = $conn;
                    break;
                }
            }
        }

        foreach ($connections as $action => $name) {
            if (!isset($conns[$name])) {
                $msg = 'DB connection config named `' . $name . '` not found';
                throw new ConnectionNotFoundException($msg);
            }

            $connections[$action] = $conns[$name];
            $connections[$action]['name'] = $name;
        }

        if ($connections['read']['driver'] != $connections['write']['driver']) {
            $msg = 'Read and write driver for model `' . $model . '` is different';
            throw new InvalidConnectionDriverException($msg);
        }

        $options['connections'] = $connections;

        $driver = $connections['read']['driver'];

        if (!isset($configs['drivers'][$driver])) {
            $msg = 'No model driver for `' . $driver . '` registered';
            throw new DriverNotInstalledException($msg);
        }

        $handler = $configs['drivers'][$driver];

        Model::$models[$model] = new $handler($options);
    }

    public static function __callStatic($name, $args)
    {
        $model = get_called_class();

        if (!isset(Model::$models[$model])) {
            Model::buildModel($model);
        }

        $model = Model::$models[$model];

        return call_user_func_array([$model, $name], $args);
    }

    public static function refresh(): void
    {
        Model::$models = [];
    }
}
