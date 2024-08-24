<?php

/**
 * ConvertController
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

use App\Services\RateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * ConvertController
 * php version 8.3
 *
 * @category     Controllers
 *
 * @author       appsinvest <appscenter@proton.me>
 * @license      GPLv3 License
 *
 * @link         https://github.com/appsinvest/btc-converter-service
 */
class ConvertController extends Controller
{
    public function __construct(private readonly RateService $rateService)
    {
    }

    /**
     * Convert
     */
    #[OA\Post(
        path: '/api/v1?method=convert&=',
        summary: 'Convert currencies',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['currency_from', 'currency_to', 'value'],
                    properties: [
                        new OA\Property(
                            property: 'currency_from',
                            description: 'Source currency',
                            type: 'string',
                            example: 'BTC',
                        ),
                        new OA\Property(
                            property: 'currency_to',
                            description: 'Destination currency',
                            type: 'string',
                            example: 'USD',
                        ),
                        new OA\Property(property: 'value', description: 'Amount', type: 'number'),
                    ]
                )
            )
        ),
        tags: ['Convert'],
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
    public function __invoke(): Response|JsonResponse|null
    {
        $request = Request::capture();

        $value = (float) $request->get('value');

        $from = strtoupper(trim($request->get('currency_from')));
        $to = strtoupper(trim($request->get('currency_to')));

        $convertDTO = $this->rateService->convert($from, $to, $value);

        return response()->json([
            'status' => 'success',
            'code' => Response::HTTP_OK,
            'data' => [
                'currency_from' => $from,
                'currency_to' => $to,
                'value' => $value,
                'converted_value' => $convertDTO->getConvertedValue(),
                'rate' => $convertDTO->getRate(),
            ],
        ]);
    }
}
