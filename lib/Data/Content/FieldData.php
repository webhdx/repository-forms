<?php

namespace EzSystems\RepositoryForms\Data\Content;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        FieldData::class,
        \EzSystems\EzPlatformContentForms\Data\Content\FieldData::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformContentForms\Data\Content\FieldData::class)) {
    /**
     * @deprecated Class FieldData has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformContentForms\Data\Content\FieldData class instead.
     */
    class FieldData {}
}
