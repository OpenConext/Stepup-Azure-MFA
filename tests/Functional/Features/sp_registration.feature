@remote
Feature: When an user needs to register for a new token
  To register an user for a new token
  As a service provider
  I need to send an AuthnRequest to the identity provider

  Scenario: When an user needs to register for a new token
    # The user request a registration from the service provider
    Given I am on "https://azure-mfa.stepup.example.com/demo/sp"
    Then I should see "Demo service provider"
    When I press "Register user"

    # The user register himself at the IdP
    Then I should be on "https://azure-mfa.stepup.example.com/registration"
    And I should see "Registration"

    # GSSP assigns a subject name id to the user
    Given I fill in "Email address" with "user@stepup.example.com"
    When I press "Submit"

    # The MFA SSO page
    Then I should be on "https://azure-mfa.stepup.example.com/mock/sso"
    Given I press "Submit-success"

    # The GSSP acs page.
    Then I should be on "https://azure-mfa.stepup.example.com/saml/sso_return"
    And I press "Submit"

    # Back at the SP.
    Then I should be on "https://azure-mfa.stepup.example.com/demo/sp/acs"
    And I should see "Demo Service provider ConsumerAssertionService endpoint"
    And I should see "urn:oasis:names:tc:SAML:2.0:status:Succes"
    And I should see a NameID with email address "user@stepup.example.com"
