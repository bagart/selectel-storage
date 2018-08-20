<?php

namespace BAGArt\SelectelStorage;

class SelectelService extends \OpenCloud\ObjectStore\Service
{
    public function getAccount()
    {
        return new SelectelResourceAccount($this);
    }
}