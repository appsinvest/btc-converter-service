<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Redis\Connections\Connection;
use RedisException;

readonly class TokenService
{
    public function __construct(private Connection $redisConnection)
    {
    }

    /**
     * @throws RedisException
     */
    public function encode(string $token, int $ttl): string
    {
        $key = fake()->regexify('[A-Za-z0-9_\-]{64}');
        $this->redisConnection->client()->set($key, $token);
        $this->redisConnection->client()->expire($key, $ttl * 60);

        return $key;
    }

    /**
     * @throws RedisException
     */
    public function delete(string $token): void
    {
        $this->redisConnection->client()->del($token);
    }
}
