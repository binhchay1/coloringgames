<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace LassoVendor\Symfony\Component\HttpClient;

use LassoVendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use LassoVendor\Symfony\Contracts\HttpClient\ResponseInterface;
use LassoVendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use LassoVendor\Symfony\Contracts\Service\ResetInterface;
/**
 * Eases with writing decorators.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait DecoratorTrait
{
    private $client;
    public function __construct(HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }
    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $options = []) : ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }
    /**
     * {@inheritdoc}
     */
    public function stream($responses, float $timeout = null) : ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
    /**
     * {@inheritdoc}
     */
    public function withOptions(array $options) : self
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);
        return $clone;
    }
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
