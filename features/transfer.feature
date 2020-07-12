Feature:
  In order to transfer money from one account to another
  As a client software developer
  I need to be to perform transfer and get result through the API.

  @createSchema
  @dropSchema
  Scenario: Do a transfer
    Given the following accounts exists:
      | id   | amount |
      | 2000 | 100.00 |
      | 2001 | 0.00   |

    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/accounts/transfer" with body:
    """
    {
      "accountFrom": 2000,
      "accountTo": 2001,
      "amount": "50.01"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON should be equal to:
    """
    {
      "accountFrom": {
          "id": 2000,
          "amount": 49.99
      },
      "accountTo": {
        "id": 2001,
        "amount": 50.01
      }
    }
    """
