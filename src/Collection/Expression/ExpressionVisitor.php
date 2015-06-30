<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection\Expression;

/**
 * An Expression visitor walks a graph of expressions and turns them into a
 * query for the underlying implementation.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
abstract class ExpressionVisitor {

    /**
     * Convert a comparison expression into the target query language output.
     *
     * @param Comparison $comparison
     *
     * @return mixed
     */
    abstract public function walkComparison(Comparison $comparison);

    /**
     * Convert a composite expression into the target query language output.
     *
     * @param CompositeExpression $expr
     *
     * @return mixed
     */
    abstract public function walkCompositeExpression(CompositeExpression $expr);

    /**
     * Dispatch walking an expression to the appropriate handler.
     *
     * @param ExpressionInterface $expr
     *
     * @return mixed
     *
     * @throws ExpressionException
     */
    public function dispatch(ExpressionInterface $expr) {

        switch(true) {

            case ($expr instanceof Comparison):
                return $this->walkComparison($expr);

            case ($expr instanceof CompositeExpression):
                return $this->walkCompositeExpression($expr);

            default:
                throw ExpressionException::unknownExpression($expr);
        }
    }
} 