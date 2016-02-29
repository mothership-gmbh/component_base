<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Exception;

use Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Mothership\Exception\ExceptionAbstract.
 *
 * @category  Mothership
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
abstract class ExceptionAbstract extends Exception
{
    /**
     * @var $int
     */
    protected $gravity; //score from 0 to 100 where 100 is the most dangerous

    /**
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    protected $output;

    /**
     * @param string             $message
     * @param int                $code
     * @param Exception|null     $previous
     * @param ConsoleOutput|null $output
     * @param bool|true          $sendAlert if is true the exception will be write on the $output
     */
    public function __construct(
        $message = '',
        $code = 0,
        Exception $previous = null,
        ConsoleOutput $output = null,
        $sendAlert = true
    ) {
        parent::__construct($message, $code, $previous);
        if ($previous != null) {
            $this->message .= "\n" . $previous->getMessage();
        }

        $this->output = $output;
        if (is_null($output) || !isset($output)) {
            $this->output = new ConsoleOutput();
        }

        $this->gravity = $this->code;

        if ($sendAlert && $previous == null) {
            $this->alert();
        }
    }

    /**
     * Get the gravity of the exception.
     *
     * @return int
     */
    public function getGravity()
    {
        return $this->gravity;
    }

    /**
     * Get the gravity level of the exception.
     *
     * @return string
     */
    protected function getGravityLevel()
    {
        switch ($this->gravity) {
            case $this->gravity > 90:
                return 'danger';
            case $this->gravity >= 80 && $this->gravity < 90:
                return 'low-danger';
            case $this->gravity >= 50 && $this->gravity < 80:
                return 'warning';
            default:
                return 'info';
        }
    }

    /**
     * Exception class for some outputs.
     */
    public function alert()
    {
        $level = $this->getGravityLevel();
        switch ($level) {
            case 'danger':
                $this->output->writeln('<error>' . $this->message . '</error>');
                break;
            case 'low-danger':
                $this->output->writeln('<error>' . $this->message . '</error>');
                break;
            case 'waring':
                $this->output->writeln('<comment>' . $this->message . '</comment>');
                break;
            case 'info':
                $this->output->writeln('<info>INFO: ' . $this->message . '</info>');
                break;
        }
    }
}
