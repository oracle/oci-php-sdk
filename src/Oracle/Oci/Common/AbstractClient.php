<?php

namespace Oracle\Oci\Common;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use InvalidArgumentException;

abstract class AbstractClient 
{
    private /*AuthProviderInterface*/ $auth_provider;
    protected /*?Region*/ $region;

    protected /*string*/ $endpoint;
    protected $client;

    public function __construct(
        $endpointTemplate,
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null)
    {
        $this->auth_provider = $auth_provider;

        if ($auth_provider instanceof RegionProvider)
        {
            $this->region = $auth_provider->getRegion();
        }
        if ($region != null)
        {
            if ($region instanceof Region)
            {
                $this->region = $region;
            }
            else {
                $knownRegion = Region::getRegion($region);
                if ($knownRegion == null)
                {
                    // forward-compatibility for unknown regions
                    $realm = Realm::getRealmForUnknownRegion();
                    $endpoint = str_replace('{region}', $region, $endpointTemplate);
                    $endpoint = str_replace('{secondLevelDomain}', $realm->getRealmDomainComponent(), $endpoint);
                    $this->region = null;
                    // echo "Region $region is unknown, assuming it to be in realm $realm. Setting endpoint to $endpoint".PHP_EOL;
                }
                else
                {
                    $this->region = $knownRegion;
                }
            }
        }
        if ($this->region == null && $endpoint == null)
        {
            throw new InvalidArgumentException('Neither region nor endpoint is set.');
        }

        if ($endpoint != null) {
            $this->endpoint = $endpoint;
        }
        else {
            $this->endpoint = str_replace('{region}', $this->region->getRegionId(), $endpointTemplate);
            $this->endpoint = str_replace('{secondLevelDomain}', $this->region->getRealm()->getRealmDomainComponent(), $this->endpoint);
        }
        // echo "Final endpoint: {$this->endpoint}".PHP_EOL;

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            // echo "Request URI: " . $request->getUri() . PHP_EOL;

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

            // echo "Signing string:\n$signing_string".PHP_EOL;

            $signature = $this->sign_string($signing_string, $this->auth_provider->getKeyFilename(), $this->auth_provider->getKeyPassphrase());

            $authorization_header = "Signature version=\"1\",keyId=\"{$this->auth_provider->getKeyId()}\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
            $request = $request->withHeader('Authorization', $authorization_header);

            // echo "\nRequest headers:".PHP_EOL;
            // foreach ($request->getHeaders() as $name => $values) {
            //     echo $name . ': ' . implode(', ', $values) . "\n";
            // }

            return $request;
        }));

        $this->client = new Client([
            'handler' => $stack
        ]);
    }

    protected function sign_string($data, $key_path, $passphrase){
        $pkeyid = openssl_pkey_get_private($key_path, $passphrase);
        if (!$pkeyid) {
            exit('Error reading private key');
        }

        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }
}
?>