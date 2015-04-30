<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel\Repository;
use Cqrs\Domain\ParameterBag;
use Cqrs\ReadModel\ReadModelInterface;
use MongoDB;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/15/15
 */
class MongoReadModelRepository implements ReadModelRepositoryInterface {

	private $_collection;
	private $_class;

	/**
	 * @param MongoDB $client
	 * @param string  $collectionName
	 * @param string  $class
	 */
	public function __construct(MongoDB $client, $collectionName, $class) {

		$this->_collection  = $client->{$collectionName};
		$this->_class       = $class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(ReadModelInterface $model) {

		$this->assertInstanceOf($model);

		$params = array(
			'type'      => get_class($model),
			'id'        => $model->getId(),
			'payload'   => $model->serialize()
		);

		if ($this->find($model->getId())) {

			$this->_collection->update(array('type' => get_class($model), 'id' => $model->getId()), $params);
		}
		else {

			$this->_collection->insert($params);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id) {

		if ($record = $this->_collection->findOne(array('type' => $this->_class, 'id' => (string) $id))) {

			return $this->deserialize($record);
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findBy(array $fields) {

		$results = $this->_collection->find($this->modifyKeysForSearch($fields));

		if ($results->count()) {

			return array_map(array($this, 'deserialize'), iterator_to_array($results));
		}

		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll() {

		$results = iterator_to_array($this->_collection->find(array('type' => $this->_class)));

		return array_map(array($this, 'deserialize'), $results);
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove($id) {

		$this->_collection->remove(array('id' => (string) $id, 'type' => $this->_class));
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
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function assertInstanceOf(ReadModelInterface $model) {

		$instance = $this->_class;

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