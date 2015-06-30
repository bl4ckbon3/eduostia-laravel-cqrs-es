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
use Cqrs\Domain\DomainEventInterface;

/**
 * EventSourcedEntity used Composite Pattern to keep bounded
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
abstract class AbstractEventSourcedEntity implements EventSourcedEntityInterface {

	/**
	 * @var AbstractEventSourcedAggregateRoot
	 */
	private $_aggregateRoot;

	/**
	 * {@inheritDoc}
	 */
	public function handleRecursively(DomainEventInterface $event) {

		$this->handle($event);

		foreach($this->getChildEntities() as $entity) {

			$entity->registerAggregateRoot($this->_aggregateRoot);
			$entity->handleRecursively($event);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function registerAggregateRoot(AbstractEventSourcedAggregateRoot $aggregateRoot) {

		if ($this->_aggregateRoot !== null && $this->_aggregateRoot !== $aggregateRoot) {

			throw new \RuntimeException(sprintf(
				'Aggregate Root "%s" already registered in "%s"',
				get_class($aggregateRoot),
				get_class($this)
			));
		}

		$this->_aggregateRoot = $aggregateRoot;
	}

	/**
	 * Handle DomainEvent if possible
	 *
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function handle(DomainEventInterface $event) {

		$method = $this->getApplyMethodName($event);

		if ( ! method_exists($this, $method)) {

			return;
		}

		$this->{$method}($event);
	}

	/**
	 * Return child entities
	 *
	 * Override this method if EventSourcedEntity has children
	 *
	 * @return EventSourcedEntityInterface[]
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function getChildEntities() {

		return array();
	}

	protected function apply(DomainEventInterface $event) {

		$this->_aggregateRoot->apply($event);
	}

	/**
	 * Return method for apply DomainEvent
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