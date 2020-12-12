<?php

/**
 * Database driver interface
 * @package iqomp/model
 * @version 1.0.1
 */

namespace Iqomp\Model;

interface DriverInterface
{
    /**
     * Construct new model object
     * @param array DB connection options
     *  @param string $model Model name
     *  @param string $table Table name
     *  @param array $chains?
     *  @param array $q_field?
     *  @param array $connections
     *    @param array $read List of connection for read
     *    @param array $write List of connection for write
     */
    public function __construct(array $options);

    // public function autocommit(bool $mode, string $conn='write'): bool;

    /**
     * Count average value of field
     * @param string $field the field sum total to average
     * @param array $where Where condition
     * @return float Average value of the column
     */
    public function avg(string $field, array $where = []): float;

    /**
     * Count total rows in table
     * @param array $where Where condition
     * @return int Total row
     */
    public function count(array $where = []): int;

    /**
     * Insert single data to database
     * @param array $row Array column-value pair of data to insert
     * @param bool $ignore Ignore error data already there
     * @return int Last inserted id on success, null otherwise
     */
    public function create(array $row, bool $ignore = false): ?int;

    /**
     * Insert many data at once
     * @param array $rows List of array list data to insert
     * @param bool $ignore Ignore exists data if possible
     * @return boolean true on success false otherwise.
     */
    public function createMany(array $rows, bool $ignore = false): bool;

    // public function commit(string $conn='write'): bool;

    /**
     * Decrease multiple columns with condition
     * @param array $fields List of field-value pair of column to decrease by value
     * @param $where Where condition
     */
    public function dec(array $fields, array $where = []): bool;

    /**
     * Escape string to use in raw query
     * @param string $str String to escape
     * @return escaped string
     */
    public function escape(string $str): string;

    /**
     * Get single row from table
     * @param array $where Where condition
     * @param array $order Array list of field-direction pair of sort
     * @return object if exists or null
     */
    public function getOne(array $where = [], array $order = ['id' => false]): ?object;

    /**
     * Get multiple rows from database
     * @param array $where Where condition
     * @param int $rpp Result per page, default 0 which is all.
     * @param int $page Page number, default 1.
     * @param array $order Array list of field-direction pair of sort
     * @return array list of object or empty array
     */
    public function get(array $where = [], int $rpp = 0, int $page = 1, array $order = ['id' => false]): array;

    /**
     * Get connection object
     * @param string $target Connection type target
     * @return resource connection
     */
    public function getConnection(string $target = 'read');

    /**
     * Get connection name in config that the model use for $target connection
     * @param string $target Connection type target
     * @return string connection config name
     */
    public function getConnectionName(string $target = 'read'): ?string;

    /**
     * Get current connection database name
     * @param string $target Connection type target
     * @return string database name
     */
    public function getDBName(string $target = 'read'): ?string;

    /**
     * Get the driver name used for this model
     * @return string driver name
     */
    public function getDriver(): ?string;

    /**
     * Get the model name of current model
     * @return string model name
     */
    public function getModel(): string;

    /**
     * Get the tabel name that this model handle
     * @return string'
     */
    public function getTable(): string;

    /**
     * Increase multiple columns with condition
     * @param array $fields List of field-value pair of column to increase by value
     * @param $where Where condition
     */
    public function inc(array $fields, array $where = []): bool;

    /**
     * Return last error accured
     * @return string error message or null
     */
    public function lastError(): ?string;

    /**
     * Return last id inserted to database
     * @return int last inserted id, or null otherwise
     */
    public function lastId(): ?int;

    /**
     * Return the most last executed query
     * @return string if exists, null otherwise
     */
    public function lastQuery(): ?string;

    /**
     * Get the maximum value of field from table
     * @param string $field The field to process
     * @param array $where Where condition
     * @return int The max value of field.
     */
    public function max(string $field, array $where = []): int;

    /**
     * Get the minimum value of field from table
     * @param string $field THe field to process
     * @param array $where Where condition
     * @return int The smallest value of field.
     */
    public function min(string $field, array $where = []): int;

    /**
     * Remove row from table
     * @param array $where Where condition
     * @return boolean true on success, false otherwise.
     */
    public function remove(array $where = []): bool;

    // public function rollback(string $conn='write'): bool;

    /**
     * Update table
     * @param array $fields List of field-value pair of data to update
     * @param array $where Where condition.
     * @return boolean true on success false otherwise.
     */
    public function set(array $fields, array $where = []): bool;

    /**
     * Sum table single field.
     * @param string $field The field to sum
     * @param array $where Where conditon.
     * @return int total sum of the field value.
     */
    public function sum(string $field, array $where = []): int;

    /**
     * Truncate the table
     * @param string $target Connection target
     */
    public function truncate(string $target = 'write'): bool;
}
