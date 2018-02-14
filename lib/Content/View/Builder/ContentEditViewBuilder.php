<?php
declare(strict_types=1);

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Content\View\Builder;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

/**
 * Builds ContentEditView objects.
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

    /** @var \Symfony\Component\Form\FormFactory */
    private $formFactory;

    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        string $defaultTemplate,
        FormFactory $formFactory
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->defaultTemplate = $defaultTemplate;
        $this->formFactory = $formFactory;
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
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentEditView($this->defaultTemplate);

        $language = $this->resolveLanguage($parameters);

        $location = $this->resolveLocation($parameters);

        $content = $this->resolveContent($parameters, $location, $language);
        $contentType = $this->loadContentType((int) $content->contentInfo->contentTypeId);

        if (!$content->getVersionInfo()->isDraft()) {
            throw new InvalidArgumentException('Version', 'status is not draft');
        }

        if (null === $location) { // assume main location if no location was provided
            $location = $this->loadLocation((int) $content->contentInfo->mainLocationId);
        }

        if ($location->contentId !== $content->id) {
            throw new InvalidArgumentException('Location', 'Provided location does not belong to selected content');
        }

        $contentUpdate = $this->resolveContentEditData($content, $language, $contentType);
        $form = $this->resolveContentEditForm($contentUpdate, $language, $content);

        $view->setContent($content);
        $view->setLanguage($language);
        $view->setLocation($location);
        $view->setForm($form);

        $view->addParameters([
            'content' => $content,
            'location' => $location,
            'language' => $language,
            'contentType' => $contentType,
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @param int $contentId
     * @param array $languages
     * @param int|null $versionNo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function loadContent(int $contentId, array $languages = [], int $versionNo = null): Content
    {
        return $this->repository->getContentService()->loadContent($contentId, $languages, $versionNo);
    }

    /**
     * Loads a visible Location.
     *
     * @param int $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }

    /**
     * Loads Language with code $languageCode.
     *
     * @param string $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     */
    private function loadLanguage(string $languageCode): Language
    {
        return $this->repository->getContentLanguageService()->loadLanguage($languageCode);
    }

    /**
     * Loads ContentType with identifier $contentTypeIdentifier.
     *
     * @param int $contentTypeId
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    private function loadContentType(int $contentTypeId): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentType($contentTypeId);
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    private function resolveLanguage(array $parameters): Language
    {
        if (isset($parameters['languageCode'])) {
            return $this->loadLanguage($parameters['languageCode']);
        }

        if (isset($parameters['language'])) {
            if (is_string($parameters['language'])) {
                // @todo BC: route parameter should be called languageCode but it won't happen until 3.0
                return $this->loadLanguage($parameters['language']);
            }

            return $parameters['language'];
        }

        throw new InvalidArgumentException('Language',
            'No language information provided. Are you missing language or languageCode parameters');
    }

    /**
     * @param array $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    private function resolveContent(array $parameters, ?Location $location, Language $language): Content
    {
        if (isset($parameters['content'])) {
            return $parameters['content'];
        }

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

        return $this->loadContent(
            (int) $contentId,
            null !== $language ? [$language->languageCode] : [],
            (int) $parameters['versionNo'] ?: null
        );
    }

    /**
     * @param \EzSystems\RepositoryForms\Data\Content\ContentUpdateData $contentUpdate
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function resolveContentEditForm(
        ContentUpdateData $contentUpdate,
        Language $language,
        Content $content
    ): FormInterface {
        return $this->formFactory->create(
            ContentEditType::class,
            $contentUpdate,
            [
                'languageCode' => $language->languageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'drafts_enabled' => true,
            ]
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
     */
    private function resolveContentEditData(
        Content $content,
        Language $language,
        ContentType $contentType
    ): ContentUpdateData {
        $contentUpdateMapper = new ContentUpdateMapper();

        return $contentUpdateMapper->mapToFormData($content, [
            'languageCode' => $language->languageCode,
            'contentType' => $contentType,
        ]);
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    private function resolveLocation(array $parameters): ?Location
    {
        if (isset($parameters['locationId'])) {
            return $this->loadLocation((int) $parameters['locationId']);
        }

        if (isset($parameters['location'])) {
            return $parameters['location'];
        }

        return null;
    }
}
