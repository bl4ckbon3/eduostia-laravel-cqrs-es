<?php
/*
 * This file is part of the Larave Cqrs Es package.
 *
 * (c) UCS-TV <http://ucstv.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel\Storage;
use Cqrs\ReadModel\ReadModelInterface;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     6/30/15
 */
interface ReadModelStorageInterface {

    /**
     * Persist read model to storage
     *
     * @param ReadModelInterface $model
     * @param string $table
     * @param string $class
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function save(ReadModelInterface $model, $table, $class);

    /**
     * Find read model by id
     *
     * @param string $id
     * @param string $table
     * @param string $class
     *
     * @return ReadModelInterface|null
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function find($id, $table, $class);

    /**
     * Find read models with criteria
     *
     * @param array  $fields
     * @param string $table
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findBy(array $fields, $table);

    /**
     * Return all read models
     *
     * @param string $table
     * @param string $class
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findAll($table, $class);

    /**
     * Remove read model from storage
     *
     * @param string $id
     * @param string $table
     * @param string $class
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function remove($id, $table, $class);
}