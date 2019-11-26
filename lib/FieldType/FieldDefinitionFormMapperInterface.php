<?php


namespace EzSystems\RepositoryForms\FieldType;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        \EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface::class,
        \EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface::class)) {
    /**
     * @deprecated Class FieldDefinitionFormMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface class instead.
     */
    class FieldDefinitionFormMapperInterface {}
}
