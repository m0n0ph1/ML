<?php

namespace Rubix\ML\NeuralNet;

use Rubix\ML\Other\Structures\Matrix;

/**
 * Parameter
 *
 * This wrapper enables parameters to be identified by object hash and thus
 * used as cache keys by gradient descent optimizers such as Adam, AdaGrad,
 * and RMS Prop.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Parameter
{
    /**
     * The parameter matrix.
     *
     * @var \Rubix\ML\Other\Structures\Matrix
     */
    protected $w;

    /**
     * @return void
     */
    public function __construct(Matrix $w)
    {
        $this->w = $w;
    }

    /**
     * Update the parameter matrix.
     *
     * @param  \Rubix\ML\Other\Structures\Matrix  $step
     * @return void
     */
    public function update(Matrix $step) : void
    {
        $this->w = $this->w->subtract($step);
    }

    /**
     * @return \Rubix\ML\Other\Structures\Matrix
     */
    public function w() : Matrix
    {
        return $this->w;
    }
}
