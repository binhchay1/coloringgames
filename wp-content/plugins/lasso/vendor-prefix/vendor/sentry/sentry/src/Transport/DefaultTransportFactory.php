<?php

declare (strict_types=1);
namespace LassoVendor\Sentry\Transport;

use LassoVendor\Psr\Http\Message\RequestFactoryInterface;
use LassoVendor\Psr\Http\Message\StreamFactoryInterface;
use LassoVendor\Psr\Log\LoggerInterface;
use LassoVendor\Sentry\HttpClient\HttpClientFactoryInterface;
use LassoVendor\Sentry\Options;
use LassoVendor\Sentry\Serializer\PayloadSerializer;
/**
 * This class is the default implementation of the {@see TransportFactoryInterface}
 * interface.
 */
final class DefaultTransportFactory implements TransportFactoryInterface
{
    /**
     * @var StreamFactoryInterface A PSR-7 stream factory
     */
    private $streamFactory;
    /**
     * @var RequestFactoryInterface A PSR-7 request factory
     */
    private $requestFactory;
    /**
     * @var HttpClientFactoryInterface A factory to create the HTTP client
     */
    private $httpClientFactory;
    /**
     * @var LoggerInterface|null A PSR-3 logger
     */
    private $logger;
    /**
     * Constructor.
     *
     * @param StreamFactoryInterface     $streamFactory     The PSR-7 stream factory
     * @param RequestFactoryInterface    $requestFactory    The PSR-7 request factory
     * @param HttpClientFactoryInterface $httpClientFactory The HTTP client factory
     * @param LoggerInterface|null       $logger            An optional PSR-3 logger
     */
    public function __construct(StreamFactoryInterface $streamFactory, RequestFactoryInterface $requestFactory, HttpClientFactoryInterface $httpClientFactory, ?LoggerInterface $logger = null)
    {
        $this->streamFactory = $streamFactory;
        $this->requestFactory = $requestFactory;
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
    }
    /**
     * {@inheritdoc}
     */
    public function create(Options $options) : TransportInterface
    {
        if (null === $options->getDsn()) {
            return new NullTransport();
        }
        return new HttpTransport($options, $this->httpClientFactory->create($options), $this->streamFactory, $this->requestFactory, new PayloadSerializer($options), $this->logger);
    }
}
