<?php

namespace Oracle\Oci\Common\Logging;

use Oracle\Oci\Common\AbstractClient;

interface LogAdapterInterface
{
    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null);
    public function error($message, $logName = null, $extras = []);
    public function warn($message, $logName = null, $extras = []);
    public function info($message, $logName = null, $extras = []);
    public function debug($message, $logName = null, $extras = []);

    public function isLogEnabled($priority = LOG_INFO, $logName = null);
    public function isErrorEnabled($logName = null);
    public function isWarnEnabled($logName = null);
    public function isInfoEnabled($logName = null);
    public function isDebugEnabled($logName = null);
}

class NamedLogAdapterDecorator implements LogAdapterInterface
{
    private $logName;
    private $decoratedLogger;

    public function __construct(
        $logName,
        LogAdapterInterface $decoratedLogger
    ) {
        $this->logName = $logName;
        $this->decoratedLogger = $decoratedLogger;
    }

    private function append($logName)
    {
        if ($logName == null || strlen($logName) == 0) {
            return $this->logName;
        }
        return $this->logName . "\\" . $logName;
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        return $this->decoratedLogger->log($message, $priority, $extras, $this->append($logName));
    }

    public function error($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->error($message, $this->append($logName), $extras);
    }

    public function warn($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->warn($message, $this->append($logName), $extras);
    }

    public function info($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->info($message, $this->append($logName), $extras);
    }

    public function debug($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->debug($message, $this->append($logName), $extras);
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        return $this->decoratedLogger->isLogEnabled($priority, $this->append($logName));
    }

    public function isErrorEnabled($logName = null)
    {
        return $this->decoratedLogger->isErrorEnabled($this->append($logName));
    }

    public function isWarnEnabled($logName = null)
    {
        return $this->decoratedLogger->isWarnEnabled($this->append($logName));
    }

    public function isInfoEnabled($logName = null)
    {
        return $this->decoratedLogger->isInfoEnabled($this->append($logName));
    }

    public function isDebugEnabled($logName = null)
    {
        return $this->decoratedLogger->isDebugEnabled($this->append($logName));
    }
}

function logger($logName = null)
{
    if ($logName == null) {
        return getGlobalLogAdapter();
    } else {
        return new NamedLogAdapterDecorator($logName, getGlobalLogAdapter());
    }
}

function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
{
    return logger()->log($message, $priority, $extras, $logName);
}

function error($message, $logName = null, $extras = [])
{
    return logger()->error($message, $logName, $extras);
}

function warn($message, $logName = null, $extras = [])
{
    return logger()->warn($message, $logName, $extras);
}

function info($message, $logName = null, $extras = [])
{
    return logger()->info($message, $logName, $extras);
}

function debug($message, $logName = null, $extras = [])
{
    return logger()->debug($message, $logName, $extras);
}

class NoOpLogAdapter implements LogAdapterInterface
{
    public function __construct()
    {
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        return false;
    }

    public function isErrorEnabled($logName = null)
    {
        return false;
    }
    public function isWarnEnabled($logName = null)
    {
        return false;
    }
    public function isInfoEnabled($logName = null)
    {
        return false;
    }
    public function isDebugEnabled($logName = null)
    {
        return false;
    }

    public function error($message, $logName = null, $extras = [])
    {
    }
    public function warn($message, $logName = null, $extras = [])
    {
    }
    public function info($message, $logName = null, $extras = [])
    {
    }
    public function debug($message, $logName = null, $extras = [])
    {
    }
}

abstract class AbstractLogAdapter implements LogAdapterInterface
{
    protected $debugLevel = LOG_INFO;
    protected $perLogName = [];

    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = []
    ) {
        $this->debugLevel = $debugLevel;
        $this->perLogName = $perLogName;
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        $levelToUse = $this->debugLevel;
        if ($logName != null) {
            if (array_key_exists($logName, $this->perLogName)) {
                $levelToUse = $this->perLogName[$logName];
            } else {
                $components = explode('\\', $logName);
                $str = "";
                foreach ($components as $c) {
                    if (strlen($str) > 0) {
                        $str .= "\\";
                    }
                    $str .= $c;
                    if (array_key_exists($str, $this->perLogName)) {
                        $levelToUse = $this->perLogName[$str];
                    }
                }
            }
        }
        return ($priority <= $levelToUse);
    }

    public function info($message, $logName = null, $extras = [])
    {
        log($message, LOG_INFO, $extras, $logName);
    }

    public function debug($message, $logName = null, $extras = [])
    {
        log($message, LOG_DEBUG, $extras, $logName);
    }

    public function warn($message, $logName = null, $extras = [])
    {
        log($message, LOG_WARNING, $extras, $logName);
    }

    public function error($message, $logName = null, $extras = [])
    {
        log($message, LOG_ERR, $extras, $logName);
    }

    public function isErrorEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_ERR, $logName);
    }

    public function isWarnEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_ERR, $logName);
    }

    public function isInfoEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_ERR, $logName);
    }

    public function isDebugEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_ERR, $logName);
    }
}


class EchoLogAdapter extends AbstractLogAdapter
{
    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = []
    ) {
        parent::__construct($debugLevel, $perLogName);
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        if (!$this->isLogEnabled($priority, $logName)) {
            return;
        }
        switch ($priority) {
            case LOG_ALERT:
                $priorityStr = "[ALERT]";
                break;
            case LOG_CRIT:
                $priorityStr = "[CRIT]";
                break;
            case LOG_ERR:
                $priorityStr = "[ERR]";
                break;
            case LOG_WARNING:
                $priorityStr = "[WARN]";
                break;
            case LOG_DEBUG:
                $priorityStr = "[DEBUG]";
                break;
            default:
                $priorityStr = "[INFO]";
                break;
        }
        echo "$priorityStr ($logName) $message" . PHP_EOL;
    }
}

/*LogAdapterInterface*/ $globalLogAdapter = new NoOpLogAdapter();

function getGlobalLogAdapter() // : LogAdapterInterface
{
    global $globalLogAdapter;

    if ($globalLogAdapter == null) {
        setGlobalLogAdapter(new NoOpLogAdapter());
    }
    return $globalLogAdapter;
}

function setGlobalLogAdapter(LogAdapterInterface $logAdapter)
{
    global $globalLogAdapter;

    $globalLogAdapter = $logAdapter;
}
