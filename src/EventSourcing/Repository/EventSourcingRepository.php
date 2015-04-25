<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing\Repository;
use Cqrs\Domain\AggregateRootInterface;
use Cqrs\Domain\DomainEventStreamInterface;
use Cqrs\Domain\DomainMessageInterface;
use Cqrs\EventSourcing\AggregateRootFactory;
use Cqrs\EventSourcing\EventStore\EventStoreInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class EventSourcingRepository implements EventSourcingRepositoryInterface {

	private $_eventStore;
	private $_eventDispatcher;
	private $_aggregateRootClass;
	private $_table;

	/**
	 * Initialize new instance
	 *
	 * @param EventStoreInterface $eventStore
	 * @param Dispatcher          $eventDispatcher
	 * @param string              $aggregateRootClass
	 * @param string              $table
	 */
	public function __construct(

		EventStoreInterface $eventStore,
		Dispatcher $eventDispatcher,
		$aggregateRootClass,
		$table
	) {

		$this->_eventStore          = $eventStore;
		$this->_eventDispatcher     = $eventDispatcher;
		$this->_aggregateRootClass  = $aggregateRootClass;
		$this->_table               = $table;
	}

	/**
	 * Persist related Event in AggregateRoot and raise Denormalizer
	 *
	 * @param AggregateRootInterface $aggregateRoot
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function persist(AggregateRootInterface $aggregateRoot) {

		$this->assertInstanceOf($aggregateRoot, $this->_aggregateRootClass);

		$eventStream = $aggregateRoot->getUncommittedEvents();

		if ($eventStream->count()) {

			$this->_eventStore->append($this->_table, $eventStream);
			$this->publish($eventStream);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($aggregateRootId) {

		try {

			$eventStream = $this->_eventStore->find($this->_table, $aggregateRootId);

			return AggregateRootFactory::create($this->_aggregateRootClass, $eventStream);
		}
		catch(\RuntimeException $e) {

			throw new \RuntimeException(sprintf('%s with id "%s" not found.', $this->_aggregateRootClass, $aggregateRootId));
		}
	}

	/**
	 * Publish all events in DomainEventStream for denormalizer
	 *
	 * @param DomainEventStreamInterface $domainEventStream
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function publish(DomainEventStreamInterface $domainEventStream) {

		foreach($domainEventStream as $domainMessage) {

			/**
			 * @var DomainMessageInterface $domainMessage
			 */
			$this->_eventDispatcher->fire($domainMessage->getPayload());
		}
	}

	/**
	 * Assert that $aggregateRoot must be instance of AggregateRoot $className
	 *
	 * @param AggregateRootInterface $aggregateRoot
	 * @param string                 $className
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function assertInstanceOf(AggregateRootInterface $aggregateRoot, $className) {

		if ( ! ($aggregateRoot instanceof $className)) {

			throw new \RuntimeException(sprintf(
				'Class "%s" was expected to be instanceof of "%s" but is not.',
				get_class($aggregateRoot),
				$className
			));
		}
	}
}