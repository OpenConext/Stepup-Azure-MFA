<?php

declare(strict_types = 1);

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

namespace Surfnet\AzureMfa\Domain\Institution\ValueObject;

use Surfnet\AzureMfa\Domain\Exception\InvalidInstitutionException;
use Surfnet\AzureMfa\Domain\Institution\Collection\EmailDomainCollection;

class Institution
{
    public function __construct(
        private string $name,
        private readonly IdentityProviderInterface $identityProvider,
        private readonly EmailDomainCollection $emailDomainCollection
    ) {
        if ($name === '') {
            throw new InvalidInstitutionException('The name for the institution can not be an empty string.');
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentityProvider(): IdentityProviderInterface
    {
        return $this->identityProvider;
    }

    public function getEmailDomainCollection(): EmailDomainCollection
    {
        return $this->emailDomainCollection;
    }
}
