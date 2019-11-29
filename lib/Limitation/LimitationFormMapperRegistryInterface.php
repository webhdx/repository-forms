<?php

namespace EzSystems\RepositoryForms\Limitation;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        LimitationFormMapperRegistryInterface::class,
        \EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperRegistryInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperRegistryInterface::class)) {
    /**
     * @deprecated Class LimitationFormMapperRegistryInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperRegistryInterface class instead.
     */
    interface LimitationFormMapperRegistryInterface {}
}
