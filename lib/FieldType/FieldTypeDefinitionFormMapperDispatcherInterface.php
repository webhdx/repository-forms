<?php

namespace EzSystems\RepositoryForms\FieldType;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        FieldTypeDefinitionFormMapperDispatcherInterface::class,
        \EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface::class)) {
    /**
     * @deprecated Class FieldTypeDefinitionFormMapperDispatcherInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface class instead.
     */
    interface FieldTypeDefinitionFormMapperDispatcherInterface {}
}
