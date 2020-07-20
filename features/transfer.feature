Feature:
  In order to transfer money from one account to another
  As a client software developer
  I need to be able to perform transfer and get result through the API.

  @createSchema
  Scenario: Do a transfer
    Given the following accounts exists:
      | id   | amount |
      | 2000 | 100.00 |
      | 2001 | 0.00   |

    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
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
      "message": "OK",
        "result": {
        "accountFrom": {
            "id": 2000,
            "amount": 49.99
        },
        "accountTo": {
          "id": 2001,
          "amount": 50.01
        }
      }
    }
    """

  @dropSchema
  Scenario: Cannot do a transfer with amount more than actual
    Given the following accounts exists:
      | id   | amount |
      | 2002 | 100.00 |
      | 2003 | 0.00   |

    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/accounts/transfer" with body:
    """
    {
      "accountFrom": 2002,
      "accountTo": 2003,
      "amount": "101.01"
    }
    """
    Then the response status code should be 500
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "message" should not contain "OK"
    And the JSON node "result.accountFrom.id" should be equal to the number 2002
    And the JSON node "result.accountFrom.amount" should be equal to the string "100"
    And the JSON node "result.accountTo.id" should be equal to the number 2003
    And the JSON node "result.accountTo.amount" should be equal to the string "0"

