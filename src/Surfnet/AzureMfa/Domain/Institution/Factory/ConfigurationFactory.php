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

namespace Surfnet\AzureMfa\Domain\Institution\Factory;

use Surfnet\AzureMfa\Domain\Institution\Collection\CertificateCollection;
use Surfnet\AzureMfa\Domain\Institution\Collection\EmailDomainCollection;
use Surfnet\AzureMfa\Domain\Institution\Configuration\ConfigurationValidatorInterface;
use Surfnet\AzureMfa\Domain\Institution\Configuration\InstitutionConfigurationInterface;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Certificate;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Destination;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomain;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomainInterface;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomainWildcard;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EntityId;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Institution;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\InstitutionConfiguration;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) - This factory, by design has high coupling. Coupling could be
 * reduced by introducing additional factories, but this would only complicate the simple creation logic that is now
 * used
 */
class ConfigurationFactory
{
    private readonly array $configurationData;

    public function __construct(
        ConfigurationValidatorInterface $validator,
        private readonly IdentityProviderFactoryInterface $identityProviderFactory
    ) {
        $this->configurationData = $validator->process()['institutions'];
    }

    public function build(): InstitutionConfigurationInterface
    {
        $institutions = [];
        foreach ($this->configurationData as $institutionName => $institutionData) {
            $ssoLocation = new Destination($institutionData['sso_location']);
            $entityId = new EntityId($institutionData['entity_id']);

            $emailDomains = $this->buildEmailDomains($institutionData['email_domains']);
            $certificates = $this->buildCertificates($institutionData['certificates']);

            $identityProvider = $this->identityProviderFactory->build($entityId, $ssoLocation, $certificates, $institutionData['is_azure_ad']);

            $institutions[$institutionName] = new Institution(
                $institutionName,
                $identityProvider,
                $emailDomains
            );
        }

        return new InstitutionConfiguration($institutions);
    }

    /**
     * @param array<string> $emailDomains
     */
    private function buildEmailDomains(array $emailDomains): EmailDomainCollection
    {
        $domainCollection = [];
        foreach ($emailDomains as $domain) {
            $domainCollection[] = $this->buildEmailDomain($domain);
        }
        return new EmailDomainCollection($domainCollection);
    }

    private function buildEmailDomain(string $domain): EmailDomainInterface
    {
        if (substr($domain, 0, 1) === EmailDomainWildcard::WILDCARD_CHARACTER) {
            return new EmailDomainWildcard($domain);
        }
        return new EmailDomain($domain);
    }

    /**
     * @param array<string> $certificates
     */
    private function buildCertificates(array $certificates): CertificateCollection
    {
        $certCollection = new CertificateCollection();
        foreach ($certificates as $certData) {
            $certCollection->add(new Certificate($certData));
        }
        return $certCollection;
    }
}
