<?php
/*
 * This file is part of the Soccer-Api package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Domain;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/24/15
 */
class ParameterBag implements \Countable, \IteratorAggregate {

	private $_parameters = array();

	/**
	 * @param array $params
	 */
	public function __construct(array $params) {

		$this->_parameters = $params;
	}

	/**
	 * Return parameter by index
	 *
	 * @param int $index
	 *
	 * @return mixed
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function get($index) {

		if (isset($this->_parameters[$index])) {

			return $this->_parameters[$index];
		}

		return null;
	}

	/**
	 * Set parameter by index
	 *
	 * @param int   $index
	 * @param mixed $value
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function set($index, $value) {

		$this->_parameters[$index] = $value;
	}

	/**
	 * Remove specific parameter
	 *
	 * @param int $index
	 *
	 * @return null|array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function remove($index) {

		if (isset($this->_parameters[$index]) || array_key_exists($index, $this->_parameters)) {

			$removed = $this->_parameters[$index];
			unset($this->_parameters[$index]);

			return $removed;
		}

		return null;
	}

	/**
	 * Return all parameters
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function all() {

		return $this->_parameters;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator() {

		return new \ArrayIterator($this->_parameters);
	}

	/**
	 * {@inheritDoc}
	 */
	public function count() {

		return count($this->_parameters);
	}
}