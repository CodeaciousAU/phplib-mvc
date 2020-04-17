<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Listener;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Log\Logger;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

/**
 * Listen for error events emitted from any module, and log them.
 */
class ErrorListener extends AbstractListenerAggregate
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var bool
     */
    private $didDispatchError = false;
    
    
    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();
        
        $this->listeners[] = $sharedEvents->attach(
            '*',
            'error',
            array($this, 'onError')
        );
        
        $this->listeners[] = $sharedEvents->attach(
            Application::class,
            MvcEvent::EVENT_DISPATCH_ERROR,
            array($this, 'onDispatchError'),
            101 //higher priority than the APIProblemListener (because it stops propagation)
        );

        $this->listeners[] = $sharedEvents->attach(
            Application::class,
            MvcEvent::EVENT_RENDER_ERROR,
            array($this, 'onRenderError'),
            101
        );

        $this->listeners[] = $sharedEvents->attach(
            Application::class,
            MvcEvent::EVENT_FINISH,
            array($this, 'onFinish')
        );
    }
    
    /**
     * @param EventInterface $e
     * @return void
     */
    public function onError(EventInterface $e)
    {
        $message = $e->getParam('message');
        $priority = $e->getParam('priority', Logger::ERR);
        $exception = $e->getParam('exception');

        if ($exception)
        {
            if (empty($message))
                $message = $this->renderException($exception);
            else
                $message .= PHP_EOL.$this->renderException($exception);
        }
        else if (empty($message))
            $message = 'No log message';
        
        $this->logger->log($priority, $message);
    }
    
    /**
     * @param EventInterface $e
     * @return void
     */
    public function onDispatchError(EventInterface $e)
    {
        if (($ex = $e->getParam('exception')))
        {
            $this->didDispatchError = true;
            $this->logger->log(Logger::CRIT, $this->renderException($ex));
        }
    }

    /**
     * @param EventInterface $e
     * @return void
     */
    public function onRenderError(EventInterface $e)
    {
        if (($ex = $e->getParam('exception')))
        {
            $this->didDispatchError = true;
            $this->logger->log(Logger::CRIT, $this->renderException($ex));
        }
        else
            $this->logger->log(Logger::WARN, 'Encountered a rendering error with no exception');
    }

    /**
     * @param EventInterface $e
     * return void
     */
    public function onFinish(EventInterface $e)
    {
        //Look for any otherwise unlogged errors at request finish time. This is only necessary
        //because RestController 'handles' exceptions by returning an ApiProblemResponse
        //and this prevents the DISPATCH_ERROR event from occuring.
        if ($e instanceof MvcEvent && class_exists('Laminas\ApiTools\ApiProblem\ApiProblemResponse'))
        {
            $response = $e->getResponse();
            if ($response instanceof \Laminas\ApiTools\ApiProblem\ApiProblemResponse
                && $response->getStatusCode() == 500
                && !$this->didDispatchError)
            {
                $this->logger->log(
                    Logger::ERR,
                    'An ApiProblemResponse was generated with status 500',
                    $response->getApiProblem()->toArray()
                );
            }
        }
    }

    /**
     * Convert an exception to a neat debug string for logging.
     *
     * @param \Throwable $ex
     * @param boolean $recursive If true, include previous exceptions
     * @return string
     */
    protected function renderException($ex, $recursive=true)
    {
        $parts = [
            get_class($ex).' ('.$ex->getCode().')'.PHP_EOL
            .$ex->getMessage().PHP_EOL
            .'in '.$this->normalizePath($ex->getFile()).' line '.$ex->getLine()
        ];

        $index = 0;
        foreach ($ex->getTrace() as $frame)
        {
            $line = isset($frame['line']) ? ' line '.$frame['line'] : '';
            $path = isset($frame['file']) ? $this->normalizePath($frame['file']) : '';
            if (empty($path))
                $path = 'UNKNOWN';

            $parts[] = '#'.$index.' '.$path.$line;
            if (array_key_exists('args', $frame))
            {
                $args = [];
                foreach ($frame['args'] as $arg)
                    $args[] = is_object($arg) ? get_class($arg) : gettype($arg);
                $parts[] = '   called '.$frame['function'].'('.implode(', ', $args).')';
            }

            $index++;
        }

        if ($recursive && ($prev = $ex->getPrevious()))
        {
            $parts[] = PHP_EOL.'Caused by '.$this->renderException($prev, true);
        }

        return implode(PHP_EOL, $parts);
    }

    /**
     * Strip app base directory from a file path.
     *
     * @param string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        $basedir = getcwd();
        if (!empty($basedir))
        {
            $basedir = rtrim($basedir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            if (substr($path, 0, strlen($basedir)) == $basedir)
                return substr($path, strlen($basedir));
        }
        return $path;
    }
}
