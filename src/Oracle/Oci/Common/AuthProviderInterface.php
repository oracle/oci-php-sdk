<?php

namespace Oracle\Oci\Common;

interface AuthProviderInterface
{
    public function getKeyFilename() : string;
    public function getKeyPassphrase() : ?string;
    public function getKeyId() : string;
}

interface RegionProvider
{
    public function getRegion() : string;
}

class UserAuthProviderInterface implements AuthProviderInterface
{
    protected string $tenancy_id;
    protected string $user_id;
    protected string $fingerprint;
    protected string $key_filename;
    protected ?string $key_passphrase;

    public function __construct(
        string $tenancy_id,
        string $user_id,
        string $fingerprint,
        string $key_filename,
        string $key_passphrase = null)
    {
        $this->tenancy_id = $tenancy_id;
        $this->user_id = $user_id;
        $this->fingerprint = $fingerprint;
        $this->key_filename = $key_filename;
        $this->key_passphrase = $key_passphrase;
    }

    public function getTenancyId() : string
    {
        return $this->tenancy_id;
    }
    public function getUserId() : string
    {
        return $this->user_id;
    }
    public function getFingerprint() : string
    {
        return $this->fingerprint;
    }

    public function getKeyFilename() : string
    {
        return $this->key_filename;
    }

    public function getKeyPassphrase() : ?string
    {
        return $this->key_passphrase;
    }

    public function getKeyId() : string
    {
        return "{$this->tenancy_id}/{$this->user_id}/{$this->fingerprint}";
    }
}

?>

