# PHP Developer Test Assignment
You must complete the two tasks described below, upload the result to a public repository, 
and include the instructions and description for [**Task 2**](#_page1_x72.00_y99.59) in the README file.

**It is crucial** that you provide comprehensive instructions for deployment and testing.
It is essential that you read the description and ask questions before you start the assignment. 
You must also indicate when you plan to be ready to proceed.

<a name="_page1_x72.00_y99.59"></a>
## Task 2<br>PHP

It is necessary to implement JSON API service in **php** 8 (you can use **laravel framework**) to work with exchange rates for bitcoin (**BTC**). It should be implemented using **Docker**.

Service to get current exchange rates: <https://blockchain.info/ticker>

All API methods will be available only after authorisation, i.e. all methods should be unavailable by default and give an authorisation error.

For authorisation we will use a fixed token (64 symbols including a-z A-Z 0-9 and also symbols - and _ ), we will pass it in request headers. Type **Authorisation: Bearer.**

**Request format:** <your_domain>**/api/v1?method=**<method_name>&<parameter>=<value>

**API response format**: JSON (all responses in all scenarios must be in JSON format)

**All exchange rate values should be calculated taking into account our commission = 2%**

**API should have 2 methods:**

1) **rates**: Retrieve all exchange rates considering commission = 2% (GET request) in the format:
```
{
    "status”: "success”,
    "code”: 200,
    "data”: {
        "USD” : <rate>,
        ...
    }
}
```

In case of error:
```
{
    "status”: "error”,
    "code”: 403,
    "message”: "Invalid token”
}
```

Sorting from smaller course to larger course.

The currency of interest can be passed as parameters, in the format USD, RUB, EUR, etc. In this case, we pass the values ​​specified as the currency parameter.

2) **convert**: Request for currency exchange including commission = 2%. POST request with parameters:

   currency_from: USD<br>
   currency_to: BTC<br>
   value: 1.00<br>
   <br>
   or in the opposite direction
   <br><br>
   currency_from: BTC<br>
   currency_to: USD<br>
   value: 1.00<br>
   <br>
   If the request is successful, we return:
```
{
    "status”: "success”,
    "code”: 200,
    "data”: {
        "currency_from” : BTC,
        "currency_to” : USD, 
        "value”: 1.00, 
        "converted_value”: 1.00, 
        "rate” : 1.00,
    }
}
```

In case of error:
```
{
    "status”: "error”,
    "code”: 403,
    "message”: "Invalid token”
}
```
<p>Important, the minimum exchange is 0.01 currency from
<br>For example: USD = 0.01 changes to 0.0000005556 (count up to 10 digits)
<br>If there is an exchange from BTC to USD, round up to 0.01</p>

---

# Comments on the task
## Launch
To run the job you need to start the Docker application or start the docker service

then run the command
```shell
make run
```

After the containers are fully assembled and launched, you need to go to the link http://localhost/api/documentation in your browser

This link provides the OpenApi Swagger interface for testing job endpoints. First you need to authorize (login and password are already specified by default) and copy and paste the resulting token into the dialog box using the Authorize button

## Tests
You can run tests - to do this, you first need to run the project with the command above, then run
```shell
make test
```

---

If the error below appears, you need to specify the root folder of the project in the Docker application in Resources -> Sharing and restart the build again using the same command specified above
```
Error response from daemon: Mounts denied:
The path /www/btc-converter-service is not shared from the host and is not known to Docker.
You can configure shared paths from Docker -> Preferences... -> Resources -> File Sharing.
```
