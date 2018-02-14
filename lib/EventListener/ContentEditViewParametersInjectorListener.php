<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\EventListener;

use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Maps view data to the parameters available in the template.
 */
class ContentEditViewParametersInjectorListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ViewEvents::FILTER_VIEW_PARAMETERS => ['filterViewParameters', 10],
        ];
    }

    public function filterViewParameters(FilterViewParametersEvent $event)
    {
        /** @var ContentEditView $view */
        $view = $event->getView();
        if (!$view instanceof ContentEditView) {
            return;
        }

        $parameters = [
            'content' => $view->getContent(),
            'location' => $view->getLocation(),
            'language' => $view->getLanguage(),
        ];

        $event->getParameterBag()->add($parameters);
    }
}
