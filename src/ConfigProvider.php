<?php

/**
 * Standard model config
 * @package iqomp/model
 * @version 2.0.0
 */

namespace Iqomp\Model;

class ConfigProvider
{
    protected function getPublishedFiles(): array
    {
        $base = dirname(__DIR__) . '/publish';
        $files = $this->scanDir($base, '/');
        $result = [];

        foreach ($files as $file) {
            $source = $base . $file;
            $target = BASE_PATH . $file;

            $result[] = [
                'id' => $file,
                'description' => 'Publish file of ' . $file,
                'source' => $source,
                'destination' => $target
            ];
        }

        return $result;
    }

    protected function scanDir(string $base, string $path): array
    {
        $base_path = chop($base . $path, '/');
        $files = array_diff(scandir($base_path), ['.', '..']);
        $result = [];

        foreach ($files as $file) {
            $file_path = trim($path . '/' . $file, '/');
            $file_base = $base_path . '/' . $file;

            if (is_dir($file_base)) {
                $sub_files = $this->scanDir($base, '/' . $file_path);
                if ($sub_files) {
                    $result = array_merge($result, $sub_files);
                }
            } else {
                $result[] = '/' . $file_path;
            }
        }

        return $result;
    }

    public function __invoke()
    {
        return [
            'publish' => $this->getPublishedFiles(),
            'model' => [
                'drivers' => [],
                'models' => [],
            ],
            'formatter' => [
                'handlers' => [
                    'chain' => [
                        'handler' => 'Iqomp\\Model\\Formatter::chain',
                        'collective' => true,
                        'field' => 'id'
                    ],
                    'multiple-object' => [
                        'handler' => 'Iqomp\\Model\\Formatter::multipleObject',
                        'collective' => true,
                        'field' => null
                    ],
                    'object' => [
                        'handler' => 'Iqomp\\Model\\Formatter::object',
                        'collective' => true,
                        'field' => null
                    ],
                    'object-switch' => [
                        'handler' => 'Iqomp\\Model\\Formatter::objectSwitch',
                        'collective' => 'id',
                        'field' => null
                    ],
                    'partial' => [
                        'handler' => 'Iqomp\\Model\\Formatter::partial',
                        'collective' => true,
                        'field' => 'id'
                    ]
                ]
            ],
            'validator' => [
                'errors' => [
                    '14.0' => 'not unique',
                    '19.0' => 'not exists on db',
                    '20.0' => 'one or more not exists on db'
                ],
                'validators' => [
                    'unique' => 'Iqomp\\Model\\Validator::unique',
                    'exists' => 'Iqomp\\Model\\Validator::exists',
                    'exists-list' => 'Iqomp\\Model\\Validator::existsList'
                ]
            ]
        ];
    }
}
