<?php

namespace Rubix\Tests\Classifiers;

use Rubix\ML\Online;
use Rubix\ML\Estimator;
use Rubix\ML\Persistable;
use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Classifiers\Binary;
use Rubix\ML\Classifiers\Classifier;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\Classifiers\LogisticRegression;
use Rubix\ML\Transformers\ZScaleStandardizer;
use Rubix\ML\NeuralNet\CostFunctions\CrossEntropy;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class LogisticRegressionTest extends TestCase
{
    protected $estimator;

    protected $training;

    protected $testing;

    public function setUp()
    {
        $this->training = Labeled::restore(dirname(__DIR__) . '/iris.dataset');

        $this->testing = $this->training->randomize()->head(3);

        $this->estimator = new LogisticRegression(100, 1, new Adam(0.001), 1e-4, new CrossEntropy(), 1e-3);
    }

    public function test_build_classifier()
    {
        $this->assertInstanceOf(LogisticRegression::class, $this->estimator);
        $this->assertInstanceOf(Binary::class, $this->estimator);
        $this->assertInstanceOf(Classifier::class, $this->estimator);
        $this->assertInstanceOf(Estimator::class, $this->estimator);
        $this->assertInstanceOf(Online::class, $this->estimator);
        $this->assertInstanceOf(Probabilistic::class, $this->estimator);
        $this->assertInstanceOf(Persistable::class, $this->estimator);
    }

    public function test_make_prediction()
    {
        $this->training->randomize();

        $this->estimator->train($this->training);

        $predictions = $this->estimator->predict($this->testing);

        $this->assertEquals($this->testing->label(0), $predictions[0]);
        $this->assertEquals($this->testing->label(1), $predictions[1]);
        $this->assertEquals($this->testing->label(2), $predictions[2]);

        $probabilities = $this->estimator->proba($this->testing);

        $this->assertGreaterThanOrEqual(0.5, $probabilities[0][$this->testing->label(0)]);
        $this->assertGreaterThanOrEqual(0.5, $probabilities[1][$this->testing->label(1)]);
        $this->assertGreaterThanOrEqual(0.5, $probabilities[2][$this->testing->label(2)]);
    }

    public function test_partial_train()
    {
        $folds = $this->training->stratifiedFold(3);

        $this->estimator->train($folds[0]);

        $this->estimator->partial($folds[1]);

        $this->estimator->partial($folds[2]);

        $predictions = $this->estimator->predict($this->testing);

        $this->assertEquals($this->testing->label(0), $predictions[0]);
        $this->assertEquals($this->testing->label(1), $predictions[1]);
        $this->assertEquals($this->testing->label(2), $predictions[2]);
    }

    public function test_train_with_unlabeled()
    {
        $dataset = new Unlabeled([['bad']]);

        $this->expectException(InvalidArgumentException::class);

        $this->estimator->train($dataset);
    }

    public function test_predict_untrained()
    {
        $this->expectException(RuntimeException::class);

        $this->estimator->predict($this->testing);
    }
}
