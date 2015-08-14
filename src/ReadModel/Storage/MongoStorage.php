<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel\Storage;
use MongoDB;
use Cqrs\ReadModel\ReadModelInterface;
use Cqrs\Domain\ParameterBag;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     6/30/15
 */
class MongoStorage implements ReadModelStorageInterface {

    private $_conn;

    /**
     * @param MongoDB $conn
     */
    public function __construct(MongoDB $conn) {

        $this->_conn = $conn;
    }

    /**
     * {@inheritDoc}
     */
    public function save(ReadModelInterface $model, $collection, $class) {

        $this->assertInstanceOf($model, $class);

        $params = [
            'type'      => get_class($model),
            'id'        => $model->getId(),
            'payload'   => $model->serialize()
        ];

        if ($this->find($model->getId(), $collection, $class)) {

            $this->_conn->{$collection}->update(['type' => get_class($model), 'id' => $model->getId()], $params);
        }
        else {

            $this->_conn->{$collection}->insert($params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function find($id, $collection, $class) {

        if ($record = $this->_conn->{$collection}->findOne(['type' => $class, 'id' => (string) $id])) {

            return $this->deserialize($record);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $fields, $collection) {

        $results = $this->_conn->{$collection}->find($this->modifyKeysForSearch($fields));

        if ($results->count()) {

            return array_map([$this, 'deserialize'], iterator_to_array($results));
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function findAll($collection, $class) {

        $results = iterator_to_array($this->_conn->{$collection}->find(['type' => $class]));

        return array_map([$this, 'deserialize'], $results);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($id, $collection, $class) {

        $this->_conn->{$collection}->remove(['id' => (string) $id, 'type' => $class]);
    }

    /**
     * Get collection instance.
     *
     * @param string $collection
     *
     * @return \MongoCollection
     */
    public function getInstance($collection)
    {
        return $this->_conn->{$collection};
    }

    /**
     * Deserialize array to ReadModelInterface
     *
     * @param array $serializedObject
     *
     * @return ReadModelInterface
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    private function deserialize(array $serializedObject) {

        $this->assertKeyExists($serializedObject, 'type');
        $this->assertKeyExists($serializedObject, 'payload');

        $class = str_replace('.', '\\', $serializedObject['type']);

        if ( ! in_array('Cqrs\Serializer\SerializableInterface', class_implements($class))) {

            throw new \RuntimeException(sprintf(
                'Class "%s" doest not implement Cqrs\Serializer\SerializableInterface.',
                $class
            ));
        }

        return $class::{'deserialize'}(new ParameterBag($serializedObject['payload']));
    }

    /**
     * Assert ReadModelInterface must be instance of $this->_class
     *
     * @param ReadModelInterface $model
     * @param string             $class
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    private function assertInstanceOf(ReadModelInterface $model, $class) {

        $instance = $class;

        if ( ! ($model instanceof $instance)) {

            throw new \RuntimeException(sprintf('Class: "%s" not instance of "%s"', get_class($model), $instance));
        }
    }

    /**
     * Assert array key
     *
     * @param array  $serializeObject
     * @param string $key
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    private function assertKeyExists(array $serializeObject, $key) {

        if ( ! array_key_exists($key, $serializeObject)) {

            throw new \RuntimeException(sprintf('Key "%s" should be set', $key));
        }
    }

    /**
     * Inject payload key for search body data
     *
     * @param array $fields
     *
     * @return array
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    private function modifyKeysForSearch(array $fields) {

        $modified   = array_map(function($val) { return 'payload.' . $val; }, array_keys($fields));
        $fields     = array_combine($modified, array_values($fields));

        return $fields;
    }
}
