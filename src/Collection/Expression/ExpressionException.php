<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection\Expression;

/**
 * ExpressionException Class
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class ExpressionException extends \RuntimeException {

    public static function notInstanceOfExpression() {

        return new self("No expression given to CompositeExpression.");
    }

    public static function unknownExpression($class) {

        return new self(sprintf('Unknown Expression %s', get_class($class)));
    }

    public static function unknownExpressionOperator(Comparison $comparison) {

        return new self(sprintf('Unknown comparison operator: %s', $comparison->getOperator()));
    }

    public static function unknownCompositeLogical($logical) {

        return new self(sprintf('Unknown composite logical operator %s', $logical));
    }
} 