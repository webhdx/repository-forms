<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Content\View\Builder;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\RepositoryForms\Content\View\ContentEditView;

/**
 * Builds ContentView objects.
 *
 * @internal
 */
class ContentEditViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var string */
    private $defaultTemplate;

    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        $defaultTemplate
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function matches($argument)
    {
        return 'ez_content_edit:editVersionDraftAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView|\eZ\Publish\Core\MVC\Symfony\View\View
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException If both contentId and locationId parameters are missing
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentEditView($this->defaultTemplate);

        if (isset($parameters['locationId'])) {
            $location = $this->loadLocation($parameters['locationId']);
        } elseif (isset($parameters['location'])) {
            $location = $parameters['location'];
        } else {
            $location = null;
        }

        if (isset($parameters['content'])) {
            $content = $parameters['content'];
        } else {
            if (isset($parameters['contentId'])) {
                $contentId = $parameters['contentId'];
            } elseif (isset($location)) {
                $contentId = $location->contentId;
            } else {
                throw new InvalidArgumentException(
                    'Content',
                    'No content could be loaded from parameters'
                );
            }

            $content = $this->loadContent(
                $contentId,
                isset($parameters['languageCode']) ? [$parameters['languageCode']] : [],
                $parameters['versionNo'] ?: null
            );
        }

        $view->setContent($content);

        if (isset($location)) {
            if ($location->contentId !== $content->id) {
                throw new InvalidArgumentException(
                    'Location',
                    'Provided location does not belong to selected content'
                );
            }
        }

        if (isset($location)) {
            $view->setLocation($location);
        }

        $view->addParameters([
            'content' => $content,
            'location' => $location,
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @param mixed $contentId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    private function loadContent($contentId, $languages = [], $versionNo = null)
    {
        return $this->repository->getContentService()->loadContent($contentId, $languages, $versionNo);
    }

    /**
     * Loads a visible Location.
     *
     * @param $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation($locationId)
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }
}
