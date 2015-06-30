<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel\Repository;
use Cqrs\ReadModel\ReadModelInterface;
use Cqrs\ReadModel\Storage\ReadModelStorageInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     6/30/15
 */
abstract class ReadModelReadModelRepository implements ReadModelRepositoryInterface {

    protected $storage;
    protected $table;
    protected $class;

    /**
     * Initialize instance and bind storage
     *
     * @param ReadModelStorageInterface $storage
     * @param string                    $table
     * @param string                    $class
     */
    public function __construct(ReadModelStorageInterface $storage, $table, $class) {

        $this->storage = $storage;
    }

    /**
     * Persist read model to storage
     *
     * @param ReadModelInterface $model
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function save(ReadModelInterface $model) {

        return $this->storage->save($model, $this->table, $this->class);
    }

    /**
     * Find read model by id
     *
     * @param string $id
     *
     * @return ReadModelInterface|null
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function find($id) {

        return $this->storage->find($id, $this->table, $this->class);
    }

    /**
     * Find read models with criteria
     *
     * @param array $fields
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findBy(array $fields) {

        return $this->storage->findBy($fields, $this->table);
    }

    /**
     * Return all read models
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findAll() {

        return $this->storage->findAll($this->table, $this->class);
    }

    /**
     * Remove read model from storage
     *
     * @param string $id
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function remove($id) {

        return $this->storage->remove($id, $this->table, $this->class);
    }
}