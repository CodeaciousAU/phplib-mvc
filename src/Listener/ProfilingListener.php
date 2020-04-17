<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Listener;

use Codeacious\ProfileCollector\Collector;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;

class ProfilingListener extends AbstractListenerAggregate
{
    /**
     * @var Collector|null
     */
    private $profiler;


    public function __construct($profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        if ($this->profiler === null || !$this->profiler->isEnabled())
            return;

        $this->listeners[] = $events->getSharedManager()->attach(
            Application::class,
            MvcEvent::EVENT_DISPATCH,
            array($this, 'onPreDispatch'),
            1000
        );
    }

    /**
     * @param MvcEvent $e
     * @return void
     */
    public function onPreDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (! $routeMatch instanceof RouteMatch)
            return;

        $this->profiler->setAggregationUrl('route:'.$routeMatch->getMatchedRouteName());
    }
}