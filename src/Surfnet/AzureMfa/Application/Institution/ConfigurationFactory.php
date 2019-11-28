<?php declare(strict_types=1);

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

namespace Surfnet\AzureMfa\Application\Institution;

use Surfnet\AzureMfa\Domain\Institution\Configuration\ConfigurationValidatorInterface;
use Surfnet\AzureMfa\Domain\Institution\Configuration\InstitutionConfigurationInterface;
use Surfnet\AzureMfa\Domain\Institution\Collection\EmailDomainCollection;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Destination;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomain;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomainInterface;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\EmailDomainWildcard;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\Institution;
use Surfnet\AzureMfa\Domain\Institution\ValueObject\InstitutionConfiguration;

class ConfigurationFactory
{
    /**
     * @var array
     */
    private $configurationData;

    public function __construct(ConfigurationValidatorInterface $validator)
    {
        $this->configurationData = $validator->process()['institutions'];
    }

    public function build() : InstitutionConfigurationInterface
    {
        $institutions = [];
        foreach ($this->configurationData as $institutionName => $institutionData) {
            $destinationEndpoint = new Destination($institutionData['destination']);

            $emailDomains = $this->buildEmailDomains($institutionData['email_domains']);

            $institutions[$institutionName] = new Institution(
                $institutionName,
                $destinationEndpoint,
                $emailDomains
            );
        }

        return new InstitutionConfiguration($institutions);
    }

    private function buildEmailDomains(array $emailDomains)
    {
        $domainCollection = [];
        foreach ($emailDomains as $domain) {
            $domainCollection[] = $this->buildEmailDomain($domain);
        }
        return new EmailDomainCollection($domainCollection);
    }

    private function buildEmailDomain($domain) : EmailDomainInterface
    {
        if (substr($domain, 0, 1) === EmailDomainWildcard::WILDCARD_CHARACTER) {
            return new EmailDomainWildcard($domain);
        }
        return new EmailDomain($domain);
    }
}