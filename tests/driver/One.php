<?php

namespace Iqomp\Model\Tests\Driver;

class One implements \Iqomp\Model\DriverInterface
{
    protected $connections;
    protected $data;

    public function __construct(array $options){
        $this->model        = $options['model'];
        $this->table        = $options['table'];
        // $this->chains       = $options['chains'];
        $this->connections  = $options['connections'];
        // $this->q_field      = $options['q_field'];

        $data = file_get_contents(dirname(__DIR__) . '/data/one.json');
        $this->data = json_decode($data);
    }

    public function avg(string $field, array $where = [])
    {

    }

    public function count(array $where = []): int
    {

    }

    public function countGroup(string $field, array $where = []): array
    {

    }

    public function create(array $row, bool $ignore = false): ?int
    {

    }

    public function createMany(array $rows, bool $ignore = false): bool
    {

    }

    public function dec(array $fields, array $where = []): bool
    {

    }

    public function escape(string $str): string
    {
    }

    public function getOne(array $where = [], array $order = ['id' => false]): ?object
    {
        $table = $this->table;
        $data  = $this->data->$table;

        foreach ($data as $row) {
            foreach ($where as $key => $val) {
                if ($row->$key != $val) {
                    continue 2;
                }
            }

            return clone $row;
        }

        return null;
    }

    public function get(array $where = [], int $rpp = 0, int $page = 1, array $order = ['id' => false]): array
    {
        $result = [];
        $table  = $this->table;
        $data   = $this->data->$table;


        foreach ($data as $row) {
            foreach ($where as $key => $val) {
                $row_key = (string)$row->$key;
                if (is_array($val)) {
                    if (!in_array($row_key, $val)) {
                        continue 2;
                    }
                } elseif ($row->$key != $val) {
                    continue 2;
                }
            }

            $result[] = clone $row;
        }

        return $result;
    }

    public function getConnection(string $target = 'read')
    {

    }

    public function getConnectionName(string $target = 'read'): ?string
    {
        return $this->connections[$target]['name'];
    }

    public function getDBName(string $target = 'read'): ?string
    {

    }

    public function getDriver(): ?string
    {

    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function inc(array $fields, array $where = []): bool
    {

    }

    public function lastError(): ?string
    {

    }

    public function lastId(): ?int
    {

    }

    public function lastQuery(): ?string
    {

    }

    public function max(string $field, array $where = []): int
    {

    }

    public function min(string $field, array $where = []): int
    {

    }

    public function remove(array $where = []): bool
    {

    }

    public function set(array $fields, array $where = []): bool
    {

    }

    public function sum(string $field, array $where = []): int
    {

    }

    public function sumFs(array $fields, array $where = []): array
    {

    }

    public function truncate(string $target = 'write'): bool
    {

    }
}
