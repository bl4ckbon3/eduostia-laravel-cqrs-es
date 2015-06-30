<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Domain;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/25/15
 */
trait MessageValidatorTrait {

	/**
	 * Dynamic validate message property
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function validate() {

		$reflection = new \ReflectionObject($this);
		$properties = $reflection->getProperties();

		foreach($properties as $prop) {

			$prop->setAccessible(true);

			preg_match_all('#@(.*?)\n#s', $prop->getDocComment(), $matches);

			foreach($matches[1] as $condition) {

				if (preg_match('/^var/', $condition)) {

					$parts      = explode(' ', trim($condition));
					$condition  = end($parts);
				}

				if ( ! $this->isValid($condition, $prop->getValue($this))) {

					$message    = array_search('exception', $matches[1]);
					$message    = $message ? $matches[1][$message] : $this->getExceptionMessages($condition);

					throw new \InvalidArgumentException(sprintf(
						$message,
						trim(implode(' ', preg_split(
							'/(?=[A-Z])/',
							ucfirst($prop->getName())
						)))
					));
				}
			}
		}
	}

	/**
	 * @param string $condition
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function getExceptionMessages($condition) {

		$intMessage     = 'Field "%s" must be numeric.';
		$boolMessage    = 'Field "%s" must be yes or no.';

		$maps = [

			'required'  => 'Field "%s" is required.',
		    'string'    => 'Field "%s" must be in text format.',
		    'float'     => 'Field "%s" must be floating number.',
			'int'       => $intMessage,
			'integer'   => $intMessage,
		    'bool'      => $boolMessage,
		    'boolean'   => $boolMessage
		];

		return isset($maps[$condition]) ? $maps[$condition] : 'Field "%s" is not valid.';
	}

	/**
	 * @param string $condition
	 * @param mixed  $value
	 *
	 * @return boolean
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function isValid($condition, $value) {

		switch($condition) {

			case 'required': return !!$value;

			case 'string': return is_string($value) && !preg_match('/^[0-9]+$/', $value);

			case 'int':
			case 'integer': return preg_match('/^[0-9]+$/', $value);

			case 'float':  return $value === null || filter_var($value, FILTER_VALIDATE_FLOAT);

			case 'bool':
			case 'boolean': return filter_var($value, FILTER_VALIDATE_BOOLEAN);

			default: return true;
		}
	}
}