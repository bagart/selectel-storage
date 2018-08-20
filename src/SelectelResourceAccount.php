<?php

namespace BAGArt\SelectelStorage;

class SelectelResourceAccount extends \OpenCloud\ObjectStore\Resource\Account
{
    protected $memTempUrlSecret;

    public function presetTempUrlSecret(string $secret)
    {
        $this->memTempUrlSecret = $secret;

        return $this;
    }

    public function getTempUrlSecret()
    {
        return $this->memTempUrlSecret ?? parent::getTempUrlSecret();
    }

    public function setTempUrlSecret($secret = null)
    {
        $this->memTempUrlSecret = null;

        return parent::setTempUrlSecret($secret);
    }
}