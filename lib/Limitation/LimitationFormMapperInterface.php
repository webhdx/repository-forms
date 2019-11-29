<?php

namespace EzSystems\RepositoryForms\Limitation;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        LimitationFormMapperInterface::class,
        \EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface::class)) {
    /**
     * @deprecated Class LimitationFormMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface class instead.
     */
    interface LimitationFormMapperInterface {}
}
