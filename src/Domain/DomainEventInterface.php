<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Domain;
use Cqrs\Serializer\SerializableInterface;
use Illuminate\Contracts\Queue\ShouldBeQueued;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface DomainEventInterface extends ShouldBeQueued, SerializableInterface {

}