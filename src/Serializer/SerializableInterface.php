<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Serializer;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface SerializableInterface {

	/**
	 * Convert array data to object instance
	 *
	 * @param array $data
	 *
	 * @return object
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function deserialize(array $data);

	/**
	 * Return object payload data
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function serialize();
}