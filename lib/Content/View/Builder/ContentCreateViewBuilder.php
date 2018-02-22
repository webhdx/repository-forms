<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Content\View\Builder;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\RepositoryForms\Content\View\ContentCreateView;

/**
 * Builds ContentCreateView objects.
 *
 * @internal
 */
class ContentCreateViewBuilder implements ViewBuilder
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
        return 'ez_content_edit:createWithoutDraftAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return ContentCreateView
     *
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentCreateView($this->defaultTemplate);

        // @todo in 3.0 it should differentiate language and languageCode to allow injecting objects
        if (isset($parameters['language'])) {
            $language = $this->loadLanguage($parameters['language']);
        } else {
            throw new InvalidArgumentException(
                'Language',
                'No language could be loaded from parameters'
            );
        }

        if (isset($parameters['parentLocationId'])) {
            $location = $this->loadLocation($parameters['parentLocationId']);
        } elseif (isset($parameters['parentLocation'])) {
            $location = $parameters['parentLocation'];
        } else {
            $location = null;
        }

        if (isset($parameters['contentType'])) {
            $contentType = $parameters['contentType'];
        } else {
            if (isset($parameters['contentTypeIdentifier'])) {
                $contentTypeIdentifier = $parameters['contentTypeIdentifier'];
            } else {
                throw new InvalidArgumentException(
                    'ContentType',
                    'No content type could be loaded from parameters'
                );
            }

            $contentType = $this->loadContentType($contentTypeIdentifier, [$language->languageCode]);
        }

        $view->setContentType($contentType);
        $view->setLanguage($language);

        if (isset($location)) {
            $view->setLocation($location);
        }

        $view->addParameters([
            'contentType' => $contentType,
            'language' => $language,
            'parentLocation' => $location,
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * @param string $languageCode
     *
     * @return Language
     */
    private function loadLanguage(string $languageCode): Language
    {
        return $this->repository->getContentLanguageService()->loadLanguage($languageCode);
    }

    /**
     * @param $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation($locationId): Location
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }

    /**
     * @param string $contentTypeIdentifier
     *
     * @return ContentType
     *
     * @throws UnauthorizedException
     */
    private function loadContentType(string $contentTypeIdentifier, array $prioritizedLanguages): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $contentTypeIdentifier,
            $prioritizedLanguages
        );
    }
}
