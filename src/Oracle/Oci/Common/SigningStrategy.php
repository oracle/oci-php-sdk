<?php

namespace Oracle\Oci\Common;

use InvalidArgumentException;

function getPerOperationSigningStrategyNameHeaderName()
{
    return "x-obmcs-internal-signing-strategy-name";
}

interface SigningStrategyInterface
{
    public function getRequiredSigningHeaders($verb); // : string[]
    public function getOptionalSigningHeaders(); // : string[]
    public function skipContentHeadersForStreamingPutRequests(); // : bool
}

function getSigningStrategy($strategyName)
{
    switch (strtolower($strategyName)) {
        case (string) StandardSigningStrategy::getSingleton(): return StandardSigningStrategy::getSingleton();
        case (string) ExcludeBodySigningStrategy::getSingleton(): return ExcludeBodySigningStrategy::getSingleton();
        case (string) ObjectStorageSigningStrategy::getSingleton(): return ObjectStorageSigningStrategy::getSingleton();
        case (string) FederationSigningStrategy::getSingleton(): return FederationSigningStrategy::getSingleton();
    }
    throw new InvalidArgumentException("Unknown signing strategy: $strategyName");
}

class StandardSigningStrategy implements SigningStrategyInterface
{
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (StandardSigningStrategy::$INSTANCE == null) {
            StandardSigningStrategy::$INSTANCE = new StandardSigningStrategy();
        }
        return StandardSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (StandardSigningStrategy::$requiredHeadersToSign == null) {
            StandardSigningStrategy::$requiredHeadersToSign = requiredHeadersToSign();
        }
        if (StandardSigningStrategy::$optionalSigningHeaders == null) {
            StandardSigningStrategy::$optionalSigningHeaders = optionalSigningHeaders();
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return StandardSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return StandardSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return false;
    }

    public function __toString()
    {
        return "standard";
    }
}

class ExcludeBodySigningStrategy implements SigningStrategyInterface
{
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (ExcludeBodySigningStrategy::$INSTANCE == null) {
            ExcludeBodySigningStrategy::$INSTANCE = new ExcludeBodySigningStrategy();
        }
        return ExcludeBodySigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (ExcludeBodySigningStrategy::$requiredHeadersToSign == null) {
            ExcludeBodySigningStrategy::$requiredHeadersToSign = requiredExcludeBodyHeadersToSign();
        }
        if (ExcludeBodySigningStrategy::$optionalSigningHeaders == null) {
            ExcludeBodySigningStrategy::$optionalSigningHeaders = optionalSigningHeaders();
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return ExcludeBodySigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return ExcludeBodySigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return "exclude_body";
    }
}

class ObjectStorageSigningStrategy implements SigningStrategyInterface
{
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (ObjectStorageSigningStrategy::$INSTANCE == null) {
            ObjectStorageSigningStrategy::$INSTANCE = new ObjectStorageSigningStrategy();
        }
        return ObjectStorageSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (ObjectStorageSigningStrategy::$requiredHeadersToSign == null) {
            ObjectStorageSigningStrategy::$requiredHeadersToSign = objectStorageHeadersToSign();
        }
        if (ObjectStorageSigningStrategy::$optionalSigningHeaders == null) {
            ObjectStorageSigningStrategy::$optionalSigningHeaders = optionalSigningHeaders();
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return ObjectStorageSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return ObjectStorageSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return "object_storage";
    }
}

class FederationSigningStrategy implements SigningStrategyInterface
{
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (FederationSigningStrategy::$INSTANCE == null) {
            FederationSigningStrategy::$INSTANCE = new FederationSigningStrategy();
        }
        return FederationSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (FederationSigningStrategy::$requiredHeadersToSign == null) {
            FederationSigningStrategy::$requiredHeadersToSign = objectStorageHeadersToSign();
        }
        if (FederationSigningStrategy::$optionalSigningHeaders == null) {
            FederationSigningStrategy::$optionalSigningHeaders = optionalSigningHeaders();
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return federationRemoveHostHeader(FederationSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb)));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return FederationSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return "federation";
    }
}


class HeadersToSign
{
    private $verbToHeaders;

    public function __construct($verbToHeaders)
    {
        $this->verbToHeaders = $verbToHeaders;
    }

    public function getVerbToHeaders()
    {
        return $this->verbToHeaders;
    }

    public function getHeaders($verb)
    {
        return $this->verbToHeaders[$verb];
    }
}

function requiredHeadersToSign()
{
    return new HeadersToSign([
        "get" => generalSigningHeaders(),
        "head" => generalSigningHeaders(),
        "delete" => generalSigningHeaders(),
        "put" => allSigningHeaders(),
        "post" => allSigningHeaders(),
        "patch" => allSigningHeaders()
    ]);
}

function objectStorageHeadersToSign()
{
    return new HeadersToSign([
        "get" => generalSigningHeaders(),
        "head" => generalSigningHeaders(),
        "delete" => generalSigningHeaders(),
        "put" => generalSigningHeaders(), // PUT is a special case for Object Storage
        "post" => allSigningHeaders(),
        "patch" => allSigningHeaders()
    ]);
}

function requiredExcludeBodyHeadersToSign()
{
    return new HeadersToSign([
        "get" => generalSigningHeaders(),
        "head" => generalSigningHeaders(),
        "delete" => generalSigningHeaders(),
        "put" => generalSigningHeaders(),
        "post" => generalSigningHeaders(),
        "patch" => generalSigningHeaders()
    ]);
}

function generalSigningHeaders()
{
    return ["date", "(request-target)", "host"];
}

function bodySigningHeaders()
{
    return ["content-length", "content-type", "x-content-sha256"];
}

function allSigningHeaders()
{
    return array_merge(generalSigningHeaders(), bodySigningHeaders());
}

function federationRemoveHostHeader($headers)
{
    if (($key = array_search("host", $headers)) !== false) {
        unset($headers[$key]);
    }
    return $headers;
}

function optionalSigningHeaders()
{
    return ["x-cross-tenancy-request", "x-subscription", "opc-obo-token"];
}
