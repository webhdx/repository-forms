<?php
$classMap = [
    EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData::class => EzSystems\RepositoryForms\Data\FieldDefinitionData::class,
    EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface::class => EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface::class,
    EzSystems\EzPlatformContentForms\Data\Content\FieldData::class => EzSystems\RepositoryForms\Data\Content\FieldData::class,
    EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface::class => EzSystems\RepositoryForms\Limitation\LimitationFormMapperInterface::class,
    EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface::class => EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface::class,
];

foreach ($classMap as $newClass => $oldClass) {
    class_alias($newClass, $oldClass);
}
