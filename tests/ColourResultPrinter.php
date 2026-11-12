<?php

/**
 * Prints one coloured line per test, grouped by test class.
 * Loaded via printerFile in phpunit.xml (PHPUnit 5.7 has no coloured TestDox).
 */
class ColourResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    private $status = 'pass';

    private $prettifier;

    // colors="true" in phpunit.xml only means "auto" in 5.7, which needs an interactive terminal
    public function __construct($out = null, $verbose = false, $colors = self::COLOR_DEFAULT, $debug = false, $numberOfColumns = 80, $reverse = false)
    {
        if ($colors === self::COLOR_AUTO && getenv('NO_COLOR') === false) {
            $colors = self::COLOR_ALWAYS;
        }

        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        parent::startTestSuite($suite);

        $name = $suite->getName();

        if (class_exists($name, false) && is_subclass_of($name, 'PHPUnit_Framework_TestCase')) {
            $this->write("\n" . $this->formatWithColor('fg-white, bold', $name) . "\n");
        }
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        parent::startTest($test);

        $this->status = 'pass';
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->status = 'fail';
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->status = 'fail';
    }

    public function addWarning(PHPUnit_Framework_Test $test, PHPUnit_Framework_Warning $e, $time)
    {
        $this->status = 'warn';
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->status = 'warn';
    }

    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->status = 'warn';
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->status = 'skip';
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if ($this->prettifier === null) {
            $this->prettifier = new PHPUnit_Util_TestDox_NamePrettifier();
        }

        $styles = [
            'pass' => ['fg-green', '✔'],
            'fail' => ['fg-red, bold', '✘'],
            'warn' => ['fg-yellow, bold', '!'],
            'skip' => ['fg-cyan', '-'],
        ];

        list($colour, $symbol) = $styles[$this->status];

        $name = $test instanceof PHPUnit_Framework_TestCase
            ? $this->prettifier->prettifyTestMethod($test->getName())
            : $test->toString();

        $this->write(
            ' ' . $this->formatWithColor($colour, $symbol) . ' ' .
            ($this->status === 'pass' ? $name : $this->formatWithColor($colour, $name)) . "\n"
        );

        $this->numTestsRun++;

        if ($test instanceof PHPUnit_Framework_TestCase) {
            $this->numAssertions += $test->getNumAssertions();

            if (!$test->hasExpectationOnOutput()) {
                $this->write($test->getActualOutput());
            }
        } elseif ($test instanceof PHPUnit_Extensions_PhptTestCase) {
            $this->numAssertions++;
        }
    }
}
