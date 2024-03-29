Feature: When using AzureAD directly

  Scenario: When a user is registering a new token
    Given I send a registration request to "https://azuremfa.dev.openconext.local/saml/sso"
    Then I should see "Registration"
    And I fill in "email_address_emailAddress" with "test-user@institution-b.example.com"
    When I press "email_address_submit"
    Given the login with Azure MFA succeeds and the following attributes are released:
      | name                                                       | value                                             |
      | urn:mace:dir:attribute-def:mail                            | test-user@institution-b.example.com               |
      | http://schemas.microsoft.com/claims/authnmethodsreferences | http://schemas.microsoft.com/claims/multipleauthn |
    Then I should be on "https://azuremfa.dev.openconext.local/saml/sso_return"
    And the SAML Response should contain element "StatusCode" with attribute "Value" with attribute value "urn:oasis:names:tc:SAML:2.0:status:Success"

  Scenario: Should fail when a authnmethodsreferences attribute is not released
    Given I send a registration request to "https://azuremfa.dev.openconext.local/saml/sso"
    Then I should see "Registration"
    And I fill in "email_address_emailAddress" with "test-user@institution-b.example.com"
    When I press "email_address_submit"
    Given the login with Azure MFA succeeds and the following attributes are released:
      | name                                                       | value                                             |
      | urn:mace:dir:attribute-def:mail                            | test-user@institution-b.example.com               |
    Then I should be on "https://azuremfa.dev.openconext.local/saml/sso_return"
    And the SAML Response should contain element "StatusCode" with attribute "Value" with attribute value "urn:oasis:names:tc:SAML:2.0:status:AuthnFailed"
