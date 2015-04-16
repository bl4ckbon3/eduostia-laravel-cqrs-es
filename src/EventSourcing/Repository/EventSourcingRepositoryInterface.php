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


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface EventSourcingRepositoryInterface {

	/**
	 * Persist related Event in AggregateRoot and raise Denormalizer
	 *
	 * @param AggregateRootInterface $aggregateRoot
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function persist(AggregateRootInterface $aggregateRoot);

	/**
	 * Find AggregateRoot by aggregate root id
	 *
	 * @param $aggregateRootId
	 *
	 * @return AggregateRootInterface
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function find($aggregateRootId);
}