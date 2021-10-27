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
    /*SigningStrategyInterface*/ protected $signingStrategy;

    /*string*/ protected $endpoint;
    protected $client;

    public function __construct(
        $endpointTemplate,
        AuthProviderInterface $auth_provider,
        SigningStrategyInterface $signingStrategy,
        $region=null,
        $endpoint=null
    ) {
        $this->auth_provider = $auth_provider;
        $this->signingStrategy = $signingStrategy;

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

        $signingStrategyForOperation = $this->signingStrategy;
        if ($request->hasHeader(getPerOperationSigningStrategyNameHeaderName())) {
            $perOperationSigningStrategy = $request->getHeader(getPerOperationSigningStrategyNameHeaderName());
            $c = count($perOperationSigningStrategy);
            if ($c == 1) {
                $signingStrategyForOperation = getSigningStrategy($perOperationSigningStrategy[0]);
                $request = $request->withoutHeader(getPerOperationSigningStrategyNameHeaderName());
                $middlewareLogger->debug("Using per-operation signing strategy '$signingStrategyForOperation'.", "signing\\strategy");
            } elseif ($c > 1) {
                throw new InvalidArgumentException("Should only have one value for the " . getPerOperationSigningStrategyNameHeaderName() . " header, had $c.");
            }
        } else {
            $middlewareLogger->debug("Using per-client signing strategy '$signingStrategyForOperation'.", "signing\\strategy");
        }

        // headers required for signing
        $method = strtolower($request->getMethod());
        $headers = $signingStrategyForOperation->getRequiredSigningHeaders($method);
        $middlewareLogger->debug("Required headers: '" . implode(" ", $headers) . "'.", "signing\\strategy\\details");
        $optionalHeaders = $signingStrategyForOperation->getOptionalSigningHeaders();
        $middlewareLogger->debug("Optional headers: '" . implode(" ", $optionalHeaders) . "'.", "signing\\strategy\\details");
        foreach ($optionalHeaders as $oh) {
            if ($request->hasHeader($oh)) {
                $headers[] = $oh;
            }
        }
        $headersString = implode(" ", $headers);
        $middlewareLogger->debug("Headers used for signing: '$headersString'.", "signing\\strategy\\details");
        
        $signingParts = [];
        foreach ($headers as $h) {
            $lch = strtolower($h);
            switch ($lch) {
                case "date":
                    // example: Thu, 05 Jan 2014 21:31:40 GMT
                    $date = gmdate("D, d M Y H:i:s T", time());
                    $signingParts[] = "$lch: $date";
                    $request = $request->withHeader('Date', $date);
                    break;
                case "(request-target)":
                    $request_target = $request->getRequestTarget();
                    $signingParts[] = "$lch: $method $request_target";
                    break;
                case "host":
                    $host = $request->getHeader('Host')[0];
                    $signingParts[] = "$lch: $host";
                    break;
                case "content-length":
                    $clHeaders = $request->getHeader('Content-Length');
                    if ($clHeaders != null && count($clHeaders) > 0) {
                        $content_length = $clHeaders[0];
                    } else {
                        // if content length is 0 we still need to explicitly send the Content-Length header
                        $content_length = 0;
                        $request = $request->withHeader('Content-Length', 0);
                    }
                    $signingParts[] = "$lch: $content_length";
                    break;
                case "content-type":
                    $content_type = $request->getHeader('Content-Type')[0];
                    $signingParts[] = "$lch: $content_type";
                    break;
                case "x-content-sha256":
                    $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));
                    $signingParts[] = "$lch: $content_sha256";
                    $request = $request->withHeader('x-content-sha256', $content_sha256);
                    break;
                default:
                    $optHeaders = $request->getHeader('Content-Length');
                    if ($optHeaders != null) {
                        $c = count($optHeaders);
                        if ($c == 1) {
                            $optVal = $optHeaders[0];
                        } else {
                            throw new InvalidArgumentException("Headers to be signed must be single values, found $c values for header '$h'.");
                        }
                    } else {
                        throw new InvalidArgumentException("Headers to be signed must be single values, did not find header '$h'.");
                    }
            }
        }

        $signing_string = implode("\n", $signingParts);
        $middlewareLogger->debug("Signing string:\n$signing_string", "signing\\signature");

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
