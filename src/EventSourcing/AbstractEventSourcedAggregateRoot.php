<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing;
use Cqrs\Domain\AggregateRootInterface;
use Cqrs\Domain\DomainEventInterface;
use Cqrs\Domain\DomainEventStream;
use Cqrs\Domain\DomainEventStreamInterface;
use Cqrs\Domain\DomainMessage;
use Cqrs\Domain\DomainMessageInterface;
use Cqrs\Domain\Metadata;
use Cqrs\Domain\ParameterBag;
use Cqrs\Serializer\DynamicSerializer;
use ReflectionClass;

/**
 * AggregateRoot create Bounded Context of Entity
 *
 * Composite Pattern
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
abstract class AbstractEventSourcedAggregateRoot implements AggregateRootInterface {

	use DynamicSerializer;

	/**
	 * Version number of related events.
	 *
	 * @var int
	 */
	private $_version = 0;

	/**
	 * Pending events.
	 *
	 * @var array
	 */
	private $_uncommittedEvents = array();

	/**
	 * Protect constructor
	 */
	public final function __construct() {}

	/**
	 * Add an event to list of uncommitted events AggregateRoot
	 *
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function apply(DomainEventInterface $event) {

		$this->handleRecursively($event);

		$this->_uncommittedEvents[] = DomainMessage::create(

			$this->getAggregateRootId(),
			++$this->_version,
			new Metadata(array()),
			$event
		);
	}

	/**
	 * Initialize AggregateRoot from DomainEventStream history
	 *
	 * @param DomainEventStreamInterface $stream
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function initialize(DomainEventStreamInterface $stream) {

		foreach($stream as $message) {

			/**
			 * @var $message DomainMessageInterface
			 */
			$this->_version++;
			$this->handleRecursively($message->getPayload());
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUncommittedEvents() {

		$stream = new DomainEventStream($this->_uncommittedEvents);

		$this->_uncommittedEvents = array();

		return $stream;
	}

	/**
	 * Handle domain event recursively
	 *
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function handleRecursively(DomainEventInterface $event) {

		$this->handle($event);

		foreach($this->getChildEntities() as $entity) {

			$entity->registerAggregateRoot($this);
			$entity->handleRecursively($event);
		}
	}

	/**
	 * Handle event if possible
	 *
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function handle(DomainEventInterface $event) {

		$method = $this->getApplyMethodName($event);

		if ( ! method_exists($this, $method)) {

			return;
		}

		$this->{$method}($event);
	}

	/**
	 * Return child entities
	 *
	 * Override this method if aggregate root has children
	 *
	 * @return EventSourcedEntityInterface[]
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getChildEntities() {

		return array();
	}

	/**
	 * @param string $index
	 * @param mixed  $value
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function __set($index, $value) {

		if (property_exists($this, '_' . $index)) {

			$this->{'_' . $index} = $value;
		}
		else if (property_exists($this, $index)) {

			$this->{$index} = $value;
		}
	}

	/**
	 * Deserialize Aggregate Root
	 *
	 * @param ParameterBag $params
	 *
	 * @return static
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function deserialize(ParameterBag $params) {

		$object = new static();
		$reflection = new ReflectionClass(get_called_class());
		$properties = $reflection->getProperties();

		foreach ($properties as $prop) {
			if (preg_match('#@internal\n#s', $prop->getDocComment())) {
				continue;
			}

			$prop->setAccessible(true);
			$name = $prop->getName();
			$value = $params->get($name) ?: $params->get(preg_replace('/^_+/', '', $prop->getName()));
			$prop->setValue($object, $value);
		}

		return $object;
	}

	/**
	 * Return apply method name.
	 *
	 * @param DomainEventInterface $event
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function getApplyMethodName(DomainEventInterface $event) {

		$parts = explode('\\', get_class($event));

		return 'apply' . end($parts);
	}
}
