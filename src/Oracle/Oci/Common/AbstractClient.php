<?php

namespace Oracle\Oci\Common;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Oracle\Oci\Common\Auth\AuthProviderInterface;
use Oracle\Oci\Common\Auth\RegionProviderInterface;
use Oracle\Oci\Common\Logging\LogAdapterInterface;
use Oracle\Oci\Common\Logging\NamedLogAdapterDecorator;
use function Oracle\Oci\Common\Logging\getGlobalLogAdapter;

abstract class AbstractClient
{
    /*LogAdapterInterface*/ protected $logAdapter;

    /*AuthProviderInterface*/ protected $auth_provider;
    /*?Region*/ protected $region;

    /*string*/ protected $endpoint;
    protected $client;

    public function __construct(
        $endpointTemplate,
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null
    ) {
        $this->auth_provider = $auth_provider;

        if ($auth_provider instanceof RegionProviderInterface) {
            $this->region = $auth_provider->getRegion();
        }
        if ($region != null) {
            if ($region instanceof Region) {
                $this->region = $region;
            } else {
                $knownRegion = Region::getRegion($region);
                if ($knownRegion == null) {
                    // forward-compatibility for unknown regions
                    $realm = Realm::getRealmForUnknownRegion();
                    $endpoint = str_replace('{region}', $region, $endpointTemplate);
                    $endpoint = str_replace('{secondLevelDomain}', $realm->getRealmDomainComponent(), $endpoint);
                    $this->region = null;
                    $this->logger()->info("Region $region is unknown, assuming it to be in realm $realm. Setting endpoint to $endpoint");
                } else {
                    $this->region = $knownRegion;
                }
            }
        }
        if ($this->region == null && $endpoint == null) {
            throw new InvalidArgumentException('Neither region nor endpoint is set.');
        }

        if ($endpoint != null) {
            $this->endpoint = $endpoint;
        } else {
            $this->endpoint = str_replace('{region}', $this->region->getRegionId(), $endpointTemplate);
            $this->endpoint = str_replace('{secondLevelDomain}', $this->region->getRealm()->getRealmDomainComponent(), $this->endpoint);
        }
        $this->logger()->debug("Final endpoint: {$this->endpoint}");

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            $middlewareLogger = $this->logger("middleware");
            $middlewareLogger->debug("Request URI: " . $request->getUri(), "uri");

            // headers required for all HTTP verbs
            $headers = "date (request-target) host";

            // example: Thu, 05 Jan 2014 21:31:40 GMT
            $date=gmdate("D, d M Y H:i:s T", time());
            $method = strtolower($request->getMethod());
            $request_target = $request->getRequestTarget();
            $host = $request->getHeader('Host')[0];

            $request = $request->withHeader('Date', $date);

            $signing_string = "date: $date\n(request-target): $method $request_target\nhost: $host";

            // additional required headers for POST and PUT requests
            if ($method == 'post' || $method == 'put') {
                $clHeaders = $request->getHeader('Content-Length');
                if ($clHeaders != null && count($clHeaders) > 0) {
                    $content_length = $clHeaders[0];
                } else {
                    // if content length is 0 we still need to explicitly send the Content-Length header
                    $content_length = 0;
                    $request = $request->withHeader('Content-Length', 0);
                }

                $content_type = $request->getHeader('Content-Type')[0];
                $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));

                $request = $request->withHeader('x-content-sha256', $content_sha256);

                $headers = $headers . " content-length content-type x-content-sha256";
                $signing_string = $signing_string . "\ncontent-length: $content_length\ncontent-type: $content_type\nx-content-sha256: $content_sha256";
            }

            $middlewareLogger->debug("Signing string:\n$signing_string", "signature");

            $signature = $this->sign_string($signing_string, $this->auth_provider->getPrivateKey(), $this->auth_provider->getKeyPassphrase());

            $authorization_header = "Signature version=\"1\",keyId=\"{$this->auth_provider->getKeyId()}\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
            $request = $request->withHeader('Authorization', $authorization_header);

            if ($middlewareLogger->isDebugEnabled(LOG_DEBUG, "requestHeaders")) {
                $str = "Request headers:";
                foreach ($request->getHeaders() as $name => $values) {
                    if (is_array($values)) {
                        foreach ($values as $item) {
                            $str .= PHP_EOL . $name . ': ' . $item;
                        }
                    } else {
                        $str .= PHP_EOL . $name . ': ' . $values;
                    }
                }
                $middlewareLogger->debug($str, LOG_DEBUG, [], "requestHeaders");
            }

            return $request;
        }));

        $this->client = new Client([
            'handler' => $stack
        ]);
    }

    protected function sign_string($data, $private_key, $passphrase)
    {
        if ($private_key instanceof OpenSSLAsymmetricKey) {
            $parsedKey = $private_key;
        } else {
            $parsedKey = openssl_pkey_get_private($private_key, $passphrase);
            if (!$parsedKey) {
                throw new InvalidArgumentException('Error reading private key');
            }
        }

        openssl_sign($data, $signature, $parsedKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public function logger($logName = null) // : LogAdapterInterface
    {
        $logger = getGlobalLogAdapter();
        if ($this->logAdapter != null) {
            $logger = $this->logAdapter;
        }
        if ($logName == null || strlen($logName) == 0) {
            return new NamedLogAdapterDecorator(static::class, $logger);
        } else {
            return new NamedLogAdapterDecorator(static::class . "\\" . $logName, $logger);
        }
    }

    public function setLogAdapter(LogAdapterInterface $logAdapter)
    {
        $this->globalLogAdapter = $logAdapter;
    }
}
