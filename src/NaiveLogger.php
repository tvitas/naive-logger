<?php
namespace tvitas\NaiveLogger;
use Psr\Log\AbstractLogger;

class NaiveLogger extends AbstractLogger
{
    private $logFile = '';

    public function __construct($logFile = '')
    {
        ('' === $logFile) ? $this->logFile = __DIR__ . '/../log/naive.log' : $this->logFile = $logFile;
    }

    public function log($level, $message, array $context = array())
    {
        $dateTime = date('Y-m-d H:i:s');
        $logMsg = $message;
        if (!empty($context)) {
            $logMsg = $this->interpolate($message, $context);
        }
        $logStr = $dateTime . ' ' . strtoupper($level) . ': ' . $logMsg . PHP_EOL;
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, $logStr, FILE_APPEND);
        } else {
            file_put_contents($this->logFile, $logStr);
        }

    }

    private function interpolate($message, array $context)
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
