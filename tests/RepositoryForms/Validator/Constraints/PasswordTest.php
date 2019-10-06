<?php

declare(strict_types=1);

namespace EzSystems\RepositoryForms\Tests\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\Validator\Constraints\Password;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\Password */
    private $constraint;

    protected function setUp(): void
    {
        $this->constraint = new Password();
    }

    public function testConstruct()
    {
        $this->assertSame('ez.user.password.invalid', $this->constraint->message);
    }

    public function testValidatedBy()
    {
        $this->assertSame(PasswordValidator::class, $this->constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $this->assertSame([Password::CLASS_CONSTRAINT, Password::PROPERTY_CONSTRAINT], $this->constraint->getTargets());
    }
}
