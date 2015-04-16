<?php
/*
 * This file is part of the laravel-cqrs package.
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

/**
 * AggregateRoot create Bounded Context of Entity
 *
 * Composite Pattern
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
abstract class AbstractEventSourcedAggregateRoot implements AggregateRootInterface {

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