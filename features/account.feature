Feature: Manage accounts
  In order to manage accounts
  As a client software developer
  I need to be able to create and retrieve them through the API.

  @createSchema
  Scenario: Create an account
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/accounts" with body:
    """
    {
      "id": 3000,
      "amount": "10000.00"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "id" should be equal to the number 3000
    And the JSON node "amount" should be equal to the string "10000"

  @dropSchema
  Scenario: Retrieve an account
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/accounts/3000"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "id" should be equal to the number 3000
    And the JSON node "amount" should be equal to the string "10000"
