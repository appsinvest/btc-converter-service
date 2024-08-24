<?php

/**
 * RatesController
 * php version 8.3
 *
 * @category Controllers
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApplicationException;
use App\Services\RateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * RatesController
 * php version 8.3
 *
 * @category     Controllers
 *
 * @author       appsinvest <appscenter@proton.me>
 * @license      GPLv3 License
 *
 * @link         https://github.com/appsinvest/btc-converter-service
 */
class RatesController extends Controller
{
    public function __construct(private readonly RateService $rateService)
    {
    }

    /**
     * Get rates information
     */
    #[OA\Get(
        path: '/api/v1?method=rates&=',
        summary: 'List all rates',
        tags: ['Rates'],
        parameters: [
            new OA\Parameter(
                name: 'tickers',
                in: 'query',
                required: false,
                example: 'USD,EUR,GBP'
            ),
        ],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Successfully', content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        example: 'success'
                    ),
                    new OA\Property(
                        property: 'code',
                        type: 'number',
                        example: Response::HTTP_OK
                    ),
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: new OA\Items(
                        ),
                        collectionFormat: 'multi'
                    ),
                ],
                type: 'object'
            )),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'string',
                            example: 'error'
                        ),
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Invalid token'
                        ),
                        new OA\Property(
                            property: 'code',
                            type: 'number',
                            example: Response::HTTP_FORBIDDEN
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error'),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        $request = Request::capture();
        $tickers = $request->get('tickers');
        if ($tickers) {
            $tickers = explode(',', $tickers);
            $tickers = array_map(static function ($value) {
                return strtoupper(trim($value));
            }, $tickers);
        }
        $rates = $this->rateService->getRates($tickers);
        if (null === $rates || 0 === $rates->count()) {
            throw new ApplicationException();
        }
        return response()->json([
            'status' => 'success',
            'code' => Response::HTTP_OK,
            'data' => $rates,
        ]);
    }
}
