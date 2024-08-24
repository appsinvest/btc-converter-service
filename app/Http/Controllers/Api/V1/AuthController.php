<?php

/**
 * Api Auth Controller
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

use App\Exceptions\UnauthorizedHttpException;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use OpenApi\Attributes as OA;
use RedisException;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthController
 * php version 8.3
 *
 * @category Controllers
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(private readonly TokenService $tokenService)
    {
        $this->middleware([Authenticate::class, 'auth:api'], ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @throws RedisException
     */
    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['email', 'password'],
                    properties: [
                        new OA\Property(
                            property: 'email',
                            description: 'User email',
                            type: 'string',
                            example: 'user@example.com'
                        ),
                        new OA\Property(
                            property: 'password',
                            description: 'User password',
                            type: 'string',
                            example: 'password'
                        ),
                    ]
                )
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Login successfully response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'access_token',
                            type: 'string',
                            example: '2PZ6vooCb0aUHFBO09fbvcBSaGJrLF7-Rigk3wxHdW-Z65HOoGquB_tapC87xwIT'
                        ),
                        new OA\Property(
                            property: 'token_type',
                            type: 'string',
                            example: 'bearer'
                        ),
                        new OA\Property(
                            property: 'expires_in',
                            type: 'number',
                            example: 86400
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Unauthorized'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error'),
        ]
    )]
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            throw new UnauthorizedHttpException();
        }

        $token = $this->tokenService->encode(token: (string) $token, ttl: auth()->factory()->getTTL());

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @throws RedisException
     */
    #[OA\Get(
        path: '/api/v1/auth/logout',
        summary: 'Logout',
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Successfully logged out',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Successfully logged out'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Unauthorized'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden', content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'error',
                        type: 'string',
                        example: 'Invalid token'
                    ),
                ],
                type: 'object'
            )),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Server Error'),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth()->logout();
        $token = Request::capture()->bearerToken();
        $this->tokenService->delete($token);

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
