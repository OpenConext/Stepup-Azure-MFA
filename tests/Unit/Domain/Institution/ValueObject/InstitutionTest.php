<?php declare(strict_types = 1);

/**
 * Copyright 2019 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\AzureMfa\Test\Unit\Domain\Institution\ValueObject;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Surfnet\AzureMfa\Domain\Exception\InvalidInstitutionException;
use Surfnet\AzureMfa\Domain\Institution\Collection\EmailDomainCollection;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\IdentityProviderInterface;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Institution;

class InstitutionTest extends TestCase
{
    public function test_happy_flow()
    {
        $idp = m::mock(IdentityProviderInterface::class);
        $emailDomainCollection = m::mock(EmailDomainCollection::class);
        $institution = new Institution('stepup.example.com', $idp, $emailDomainCollection);

        $this->assertInstanceOf(Institution::class, $institution);
        $this->assertEquals('stepup.example.com', $institution->getName());
        $this->assertEquals($emailDomainCollection, $institution->getEmailDomainCollection());
    }

    public function test_empty_name_not_allowed()
    {
        $idp = m::mock(IdentityProviderInterface::class);
        $emailDomainCollection = m::mock(EmailDomainCollection::class);

        $this->expectException(InvalidInstitutionException::class);
        $this->expectExceptionMessage('The name for the institution can not be an empty string.');
        new Institution('', $idp, $emailDomainCollection);
    }

    public function test_retrieve_idenitty_provider()
    {
        $idp = m::mock(IdentityProviderInterface::class);
        $emailDomainCollection = m::mock(EmailDomainCollection::class);
        $institution = new Institution('stepup.example.com', $idp, $emailDomainCollection);

        $this->assertInstanceOf(IdentityProviderInterface::class, $institution->getIdentityProvider());
        $this->assertEquals($idp, $institution->getIdentityProvider());
    }
}