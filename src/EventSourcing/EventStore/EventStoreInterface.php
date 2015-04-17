<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing\EventStore;
use Cqrs\Domain\DomainEventStreamInterface;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface EventStoreInterface {

	/**
	 * Set table name
	 *
	 * @param string $table
	 *
	 * @return static
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function from($table);

	/**
	 * Find events by id in store and make stream
	 *
	 * @param mixed $id
	 *
	 * @return DomainEventStreamInterface
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function find($id);

	/**
	 * Persist events from DomainEventStream
	 *
	 * @param DomainEventStreamInterface $eventStream
	 *
	 * @return mixed
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function append(DomainEventStreamInterface $eventStream);
}