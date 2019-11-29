<?php

namespace EzSystems\RepositoryForms\Limitation;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        LimitationValueMapperRegistryInterface::class,
        \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface::class)) {
    /**
     * @deprecated Class LimitationValueMapperRegistryInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface class instead.
     */
    interface LimitationValueMapperRegistryInterface {}
}
