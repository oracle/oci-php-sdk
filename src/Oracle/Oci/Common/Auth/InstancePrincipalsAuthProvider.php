<?php

namespace Oracle\Oci\Common\Auth;

use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\StringUtils;

class KeyPair
{
    private $publicKey;

    /**
     * Either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    private $privateKey;
    
    public function __construct(
        $publicKey,
        $privateKey
    ) {
        if (is_string($publicKey) && strpos($publicKey, "PRIVATE KEY") !== false) {
            throw new InvalidArgumentException("Private key provided as public key.");
        }
        if (is_string($privateKey) && strpos($privateKey, "PUBLIC KEY") !== false) {
            throw new InvalidArgumentException("Public key provided as private key.");
        }
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Return the private key.
     *
     * @return OpenSSLAsymmetricKey|string either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}

interface SessionKeySupplierInterface
{
    public function getKeyPair(); // : KeyPair
    public function refreshKeys();
    public function getKeyPassphrase(); // : ?string;
}

/**
 * This is a helper class to generate in-memory temporary session keys.
 */
class SessionKeySupplierImpl implements SessionKeySupplierInterface
{
    /*KeyPair*/ private $keyPair = null;

    public function __construct()
    {
        $this->keyPair = $this->generateKeyPair();
    }

    public function getKeyPair() // : KeyPair
    {
        return $this->keyPair;
    }

    public function refreshKeys()
    {
        $this->keyPair = $this->generateKeyPair();
    }

    public function getKeyPassphrase() // : ?string
    {
        return null;
    }

    protected function generateKeyPair()
    {
        $config = array(
            // TODO: what is the "digest_alg" setting? I can't find that in the OCI Java SDK
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
           
        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $details = openssl_pkey_get_details($res);
        $pubKey = $details["key"];

        return new KeyPair($pubKey, $privKey);
    }
}

class CachingSessionKeySupplier implements SessionKeySupplierInterface
{
    /*SessionKeySupplierInterface*/ private $inner;
    /*KeyPair*/ private $cachedKeyPair = null;

    public function __construct(
        SessionKeySupplierInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function getKeyPair()
    {
        if ($this->cachedKeyPair == null) {
            $this->cacheKeyPair();
        }
        return $this->cachedKeyPair;
    }

    public function refreshKeys()
    {
        $this->inner->refreshKeys();
        $this->cacheKeyPair();
    }

    public function getKeyPassphrase()
    {
        $this->inner->getKeyPassphrase();
    }

    protected function cacheKeyPair()
    {
        if ($this->inner->getKeyPair()->getPrivateKey() instanceof OpenSSLAsymmetricKey) {
            $parsedKey = $this->inner->getKeyPair()->getPrivateKey();
        } else {
            $parsedKey = openssl_pkey_get_private($this->inner->getKeyPair()->getPrivateKey(), $this->inner->getKeyPassphrase());
            if (!$parsedKey) {
                throw new InvalidArgumentException('Error reading private key');
            }
        }
        $this->cachedKeyPair = new KeyPair($this->inner->getKeyPair()->getPublicKey(), $parsedKey);
    }
}

abstract class AbstractRequestingAuthenticationDetailsProvider implements AuthProviderInterface
{
    /*FederationClientInterface*/ private $federationClient;
    /*SessionKeySupplierInterface*/ private $sessionKeySupplier;

    protected function getFederationClient()
    {
        return $this->federationClient;
    }

    protected function getSessionKeySupplier()
    {
        return $this->sessionKeySupplier;
    }

    public function __construct(
        FederationClientInterface $federationClient,
        SessionKeySupplierInterface $sessionKeySupplier
    ) {
        $this->federationClient = $federationClient;
        $this->sessionKeySupplier = new CachingSessionKeySupplier($sessionKeySupplier);
    }

    public function getKeyId() // : string
    {
        return "ST$" . $this->getFederationClient()->getSecurityToken();
    }

    public function getKeyPassphrase() // : ?string
    {
        // no passphrase
        return null;
    }

    public function getPrivateKey() // : string
    {
        return $this->sessionKeySupplier->getKeyPair()->getPrivateKey();
    }
}

class InstancePrincipalsAuthProvider extends AbstractRequestingAuthenticationDetailsProvider implements AuthProviderInterface, RegionProviderInterface, RefreshableOnNotAuthenticatedInterface
{
    /*Region*/ private $region;

    public function __construct(
        FederationClientInterface $federationClient,
        SessionKeySupplierInterface $sessionKeySupplier,
        Region $region
    ) {
        parent::__construct($federationClient, $sessionKeySupplier);
        $this->region = $region;
    }

    public function getRegion() // : ?Region
    {
        return $this->region;
    }


    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    public function refresh() // : strin
    {
        return $this->getFederationClient()->refreshAndGetSecurityToken();
    }
}
