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


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface Identifier {

	/**
	 * Generate new Identifier
	 *
	 * @return Identifier
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function generate();

	/**
	 * Create an Identifier from string
	 *
	 * @param string $id
	 *
	 * @return Identifier
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function fromString($id);

	/**
	 * Check whatever same with other $identifier
	 *
	 * @param Identifier $identifier
	 *
	 * @return bool
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function equals(Identifier $identifier);

	/**
	 * Return Identifier as string
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function toString();
}