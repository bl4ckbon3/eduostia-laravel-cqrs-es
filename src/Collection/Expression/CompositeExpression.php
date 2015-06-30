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
 * Expression of Expressions combined by AND or OR operation.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class CompositeExpression implements ExpressionInterface {

    /**
     * Expression logical operator.
     */
    const LOGICAL_AND   = 'AND';
    const LOGICAL_OR    = 'OR';

    /**
     * Selected logical operator.
     *
     * @var string
     */
    private $logical;

    /**
     * Collection of Expression.
     *
     * @var ExpressionInterface[]
     */
    private $_expression = array();

    /**
     * @param string $logical
     * @param array  $expressions <ExpressionInterface>
     *
     * @throws ExpressionException
     */
    public function __construct($logical, array $expressions) {

        $this->logical = $logical;

        foreach($expressions as $expr) {

            if ( ! $expr instanceof ExpressionInterface) {

                throw ExpressionException::notInstanceOfExpression();
            }

            $this->_expression[] = $expr;
        }
    }

    /**
     * Return the list of expressions nested in this composite.
     *
     * @return ExpressionInterface[]
     */
    public function getExpressionList() {

        return $this->_expression;
    }

    /**
     * Return the logical operator of expression in composite.
     *
     * @return string
     */
    public function getLogical() {

        return $this->logical;
    }
} 