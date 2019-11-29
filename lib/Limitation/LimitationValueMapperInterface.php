<?php

namespace EzSystems\RepositoryForms\Limitation;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        LimitationValueMapperInterface::class,
        \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface::class
    ),
    E_DEPRECATED
);

if (! \class_exists(\EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface::class)) {
    /**
     * @deprecated Class LimitationValueMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface class instead.
     */
    interface LimitationValueMapperInterface {}
}
