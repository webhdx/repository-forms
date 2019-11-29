<?php


namespace EzSystems\RepositoryForms\FieldType;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        FieldValueFormMapperInterface::class,
        \EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface::class)) {
    /**
     * @deprecated Class FieldValueFormMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface class instead.
     */
    interface FieldValueFormMapperInterface {}
}
