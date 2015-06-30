<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel;
use Cqrs\Domain\DomainEventInterface;
use ReflectionClass;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     6/30/15
 */
abstract class Denormalizer {

    /**
     * @param DomainEventInterface $event
     *
     * @return ReadModelInterface|false
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    public function handle(DomainEventInterface $event) {

        $method = $this->getApplyMethodName($event);

        if ( ! method_exists($this, $method)) {

            return false;
        }

        return $this->{$method}($event);
    }

    /**
     * Denormalize DomainEventInterface to ReadModel
     *
     * @param string               $readModelClass
     * @param DomainEventInterface $event
     *
     * @return object
     *
     * @author Iqbal Maulana <iq.bluejack@gmail.com>
     */
    protected function denormalize($readModelClass, DomainEventInterface $event) {

        if ( ! in_array($readModelClass, class_implements(ReadModelInterface::class))) {

            throw new \InvalidArgumentException(sprintf('Class "%s" not implement ReadModelInterface', $readModelClass));
        }

        $reflection     = new ReflectionClass($readModelClass);
        $constructor    = $reflection->getConstructor();
        $payload        = $event->serialize();
        $parameters     = [];

        foreach($constructor->getParameters() as $param) {

            if (isset($payload[$param->getName()])) {

                $parameters[] = $payload[$param->getName()];
                continue;
            }

            $parameters[] = null;
        }

        return $reflection->newInstanceArgs($parameters);
    }

    /**
     * Return method handler name
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