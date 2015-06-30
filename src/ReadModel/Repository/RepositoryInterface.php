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
interface RepositoryInterface {

    /**
     * Persist read model to storage
     *
     * @param ReadModelInterface $model
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function save(ReadModelInterface $model);

    /**
     * Find read model by id
     *
     * @param string $id
     *
     * @return ReadModelInterface|null
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function find($id);

    /**
     * Find read models with criteria
     *
     * @param array $fields
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findBy(array $fields);

    /**
     * Return all read models
     *
     * @return ReadModelInterface[]
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function findAll();

    /**
     * Remove read model from storage
     *
     * @param string $id
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function remove($id);
}