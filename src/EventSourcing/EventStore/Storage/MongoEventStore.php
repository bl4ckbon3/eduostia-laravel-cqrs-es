<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing\EventStore\Storage;
use Cqrs\Domain\DomainEventStream;
use Cqrs\Domain\DomainMessage;
use Cqrs\Domain\ParameterBag;
use Cqrs\EventSourcing\EventStore\EventStoreInterface;
use Cqrs\Domain\DomainEventStreamInterface;
use Cqrs\Domain\DomainMessageInterface;
use Cqrs\Serializer\SerializableInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class MongoEventStore implements EventStoreInterface {

	/**
	 * @var \MongoDB
	 */
	private $_conn;

	/**
	 * @param \MongoDB $conn
	 */
	public function __construct(\MongoDB $conn) {

		$this->_conn = $conn;
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($table, $id) {

		$collection = $this->_conn->{$table};
		$cursor     = $collection->find(array('uuid' => (string) $id))->sort(array('version' => 1));
		$events     = array();

		if ($cursor->count()) {

			foreach($cursor as $record) {

				$events[] = $this->deserialize($record);
			}

			return new DomainEventStream($events);
		}

		throw new \RuntimeException(sprintf('Aggregate with id "%s" not found.', $id));
	}

	/**
	 * {@inheritDoc}
	 */
	public function append($table, DomainEventStreamInterface $eventStream) {

		$collection     = $this->_conn->{$table};
		$events         = array_map(array($this, 'serialize'), iterator_to_array($eventStream));
		call_user_func_array(array($collection, 'insert'), $events);
	}

	/**
	 * Decorate DomainMessage for persisting
	 *
	 * @param DomainMessageInterface $domainMessage
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function serialize(DomainMessageInterface $domainMessage) {

		return array(

			'uuid'      => (string) $domainMessage->getId(),
			'version'   => $domainMessage->getVersion(),
			'metadata'  => $this->source($domainMessage->getMetadata()),
			'payload'   => $this->source($domainMessage->getPayload()),
			'created'   => $domainMessage->getCreated()->format('Y-m-d\TH:i:s.uP'),
			'type'      => $domainMessage->getType()
		);
	}

	/**
	 * Convert array to DomainMessage
	 *
	 * @param array $record
	 *
	 * @return DomainMessage
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function deserialize(array $record) {

		return new DomainMessage(

			$record['uuid'],
			$record['version'],
			$this->build($record['metadata']),
			$this->build($record['payload']),
			new \DateTime($record['created'])
		);
	}

	/**
	 * Decorate object to data
	 *
	 * @param SerializableInterface $object
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function source(SerializableInterface $object) {

		return array(

			'class'     => get_class($object),
			'payload'   => $object->serialize()
		);
	}

	/**
	 * Build an object from serialized data
	 *
	 * @param array $serializedObject
	 *
	 * @return mixed
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function build(array $serializedObject) {

		$this->assertKeyExists($serializedObject, 'class');
		$this->assertKeyExists($serializedObject, 'payload');

		if ( ! in_array('Cqrs\Serializer\SerializableInterface', class_implements($serializedObject['class']))) {

			throw new \RuntimeException(sprintf(
				'Class "%s" does not implement Cqrs\Serializer\SerializableInterface',
				$serializedObject['class']
			));
		}

		return $serializedObject['class']::{'deserialize'}(new ParameterBag($serializedObject['payload']));
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
}