<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Serializer;
use Cqrs\Domain\AggregateRootInterface;
use Cqrs\Domain\Identifier;
use Cqrs\Domain\ParameterBag;
use DateTime;
use ReflectionClass;
use ReflectionObject;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/24/15
 */
trait DynamicSerializer {

	/**
	 * {@inheritDoc}
	 *
	 * @return static
	 */
	public static function deserialize(ParameterBag $data) {

		$caller = get_called_class();

		if (in_array(AggregateRootInterface::class, class_implements($caller))) {

			return $caller::{'deserialize'}($data);
		}

		$reflection     = new ReflectionClass($caller);
		$constructor    = $reflection->getConstructor();
		$parameters     = array();

		foreach($constructor->getParameters() as $param) {

			$class = $param->getClass();

			if ($class === null) {

				$parameters[] = $data->get($param->getName());
				continue;
			}

			$className  = $class->getName();
			$value      = $data->get($param->getName());

			if (in_array(Identifier::class, class_implements($className))) {

				$parameters[] = $className::{'fromString'}($value);
			}
			else if(in_array(SerializableInterface::class, class_implements($className))) {

				$parameters[] = $className::{'deserialize'}(new ParameterBag($value));
			}

		}

		return $reflection->newInstanceArgs($parameters);
	}

	/**
	 * {@inheritDoc}
	 */
	public function serialize() {

		$reflection     = new ReflectionObject($this);
		$properties     = $reflection->getProperties();
		$data           = array();

		foreach($properties as $prop) {

			if ($this->isInternal($prop)) {

				continue;
			}

			$prop->setAccessible(true);

			$value = $prop->getValue($this);

			if ($value instanceof Identifier) {

				$value = (string) $value;
			}
			else if($value instanceof SerializableInterface) {

				$value = $value->serialize();
			}

			$name = preg_replace('/^_+/', '', $prop->getName());
			$data[$name] = $value;
		}

		return $data;
	}

	/**
	 * Check property is internal use or not
	 *
	 * @param \ReflectionProperty $prop
	 *
	 * @return int
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function isInternal(\ReflectionProperty $prop) {

		return preg_match('#@internal\n#s', $prop->getDocComment());
	}
}