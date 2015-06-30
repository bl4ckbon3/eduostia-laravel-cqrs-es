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

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     6/30/15
 */
abstract class Repository implements RepositoryInterface {

    protected $storage;

    /**
     * Initialize instance and bind storage
     *
     * @param RepositoryInterface $storage
     */
    public function __construct(RepositoryInterface $storage) {

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

        return $this->storage->save($model);
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

        return $this->storage->find($id);
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

        return $this->storage->findBy($fields);
    }

    /**
     * Return all read models
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findAll() {

        return $this->storage->findAll();
    }

    /**
     * Remove read model from storage
     *
     * @param string $id
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function remove($id) {

        return $this->storage->remove($id);
    }
}