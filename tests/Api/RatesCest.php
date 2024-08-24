<?php

declare(strict_types=1);

namespace Tests\Api;

use Codeception\Util\HttpCode;
use Exception;
use RuntimeException;
use Tests\Support\ApiTester;

class RatesCest
{
    public static string $token = '';

    public function _before(ApiTester $I)
    {
    }

    // tests

    /**
     * @throws Exception
     */
    public function tryToLogin(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        self::$token = $I->grabDataFromResponseByJsonPath('access_token')[0];
    }

    public function trySuccessfullyRates(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendGet('/api/v1?method=rates&tickers=USD,EUR,GBP,BTC');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => HttpCode::OK]);
        $I->seeResponseJsonMatchesJsonPath('$.data.USD');
        $I->seeResponseJsonMatchesJsonPath('$.data.EUR');
        $I->seeResponseJsonMatchesJsonPath('$.data.GBP');
        $I->dontSeeResponseJsonMatchesJsonPath('$.data.BTC');
    }

    public function trySuccessfullyConvert(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => 200]);
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_from');
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_to');
        $I->seeResponseJsonMatchesJsonPath('$.data.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.converted_value');
        $I->seeResponseJsonMatchesJsonPath('$.data.rate');
    }

    /**
     * @throws Exception
     */
    public function trySuccessfullyTwoWaysConvertBTCUSD(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'BTC',
            'currency_to' => 'USD',
            'value' => 1,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => 200]);
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_from');
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_to');
        $I->seeResponseJsonMatchesJsonPath('$.data.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.converted_value');
        $I->seeResponseJsonMatchesJsonPath('$.data.rate');

        $valueUSD = $I->grabDataFromResponseByJsonPath('$.data.value');


        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'USD',
            'currency_to' => 'BTC',
            'value' => 1,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => 200]);
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_from');
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_to');
        $I->seeResponseJsonMatchesJsonPath('$.data.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.converted_value');
        $I->seeResponseJsonMatchesJsonPath('$.data.rate');

        $valueBTC = $I->grabDataFromResponseByJsonPath('$.data.value');

        if ((float)bcmul((string)$valueBTC[0], (string)$valueUSD[0]) !== 1.00) {
            throw new RuntimeException("Test failed. Converted value not equal to original value");
        }
    }

    /**
     * @throws Exception
     */
    public function trySuccessfullyTwoWaysConvertEURUSD(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'EUR',
            'currency_to' => 'USD',
            'value' => 1,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => 200]);
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_from');
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_to');
        $I->seeResponseJsonMatchesJsonPath('$.data.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.converted_value');
        $I->seeResponseJsonMatchesJsonPath('$.data.rate');

        $valueUSD = $I->grabDataFromResponseByJsonPath('$.data.value');

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'USD',
            'currency_to' => 'EUR',
            'value' => 1,
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'success']);
        $I->seeResponseContainsJson(['code' => 200]);
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_from');
        $I->seeResponseJsonMatchesJsonPath('$.data.currency_to');
        $I->seeResponseJsonMatchesJsonPath('$.data.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.converted_value');
        $I->seeResponseJsonMatchesJsonPath('$.data.rate');

        $valueEUR = $I->grabDataFromResponseByJsonPath('$.data.value');

        if ((float)bcmul((string)$valueEUR[0], (string)$valueUSD[0]) !== 1.00) {
            throw new RuntimeException("Test failed. Converted value not equal to original value");
        }
    }

    public function tryUnauthorizedForRatesRequest(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendGet('/api/v1?method=rates&tickers=USD');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::FORBIDDEN]);
        $I->seeResponseContainsJson(['message' => 'Invalid token']);
    }

    public function tryUnauthorizedPassInvalidToken(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(fake()->shuffleString());

        $I->sendGet('/api/v1?method=rates&tickers=USD');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::FORBIDDEN]);
        $I->seeResponseContainsJson(['message' => 'Invalid token']);
    }

    public function tryFailedPassInvalidHttpMethodName(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendPost('/api/v1', [
            'method' => 'rates',
            'tickers' => 'USD']);
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::NOT_FOUND]);
    }

    public function tryFailedPassNotExistCurrencyName(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendGet('/api/v1?method=rates&tickers=FOO');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::FORBIDDEN]);
        $I->seeResponseContainsJson(['message' => 'Invalid token']);
    }

    public function tryFailedConvertInvalidHttpMethod(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(self::$token);

        $I->sendGet('/api/v1?' . http_build_query([
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ]));
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::NOT_FOUND]);
    }

    public function tryUnauthorizedForConvertRequest(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(fake()->shuffleString());

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::FORBIDDEN]);
        $I->seeResponseContainsJson(['message' => 'Invalid token']);
    }

    public function tryFailedPassInvalidParamsConvertRequest(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->amBearerAuthenticated(fake()->shuffleString());

        $I->sendPost('/api/v1', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => -100,
        ]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['status' => 'error']);
        $I->seeResponseContainsJson(['code' => HttpCode::FORBIDDEN]);
        $I->seeResponseContainsJson(['message' => 'Invalid token']);
    }
}
