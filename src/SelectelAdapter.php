<?php

namespace BAGArt\SelectelStorage;

use Carbon\Carbon;
use League\Flysystem\Rackspace\RackspaceAdapter;
use OpenCloud\Common\Exceptions;

class SelectelAdapter extends RackspaceAdapter
{
    public function getTemporaryUrl($path, $expiration, $options)
    {
        $secret = $this->getContainer()->getService()->getAccount()->getTempUrlSecret();
        if (!$secret) {
            throw new Exceptions\ObjectError('Cannot produce temporary URL without an account secret.');
        }

//        $this->getContainer()->getObject($path);

        $method = strtoupper($options['method'] ?? 'GET');
        $expiry = Carbon::parse($expiration)->timestamp;

        if (!in_array($method, ['GET', 'PUT'])) {
            throw new Exceptions\InvalidArgumentError(sprintf(
                'Bad method [%s] for TempUrl; only GET or PUT supported',
                $method
            ));
        }

        //$urlPath = urldecode($url->getPath());
        $urlPath = urldecode('/' . $this->getContainer()->name . '/' . $path);

        $body = sprintf("%s\n%d\n%s", $method, $expiry, $urlPath);
        $hash = hash_hmac('sha1', $body, $secret);

        $url = ($options['forcePublicUrl'] ?? false
                ? $this->getContainer()->getService()->getEndpoint()->getPublicUrl()->getHost()
                : $this->getContainer()->getUrl($path)
        );

        return sprintf(
            '%s?temp_url_sig=%s&temp_url_expires=%d',
            $url,
            $hash,
            $expiry
        );
    }

    protected function getUrlForSecret(string $path): string
    {
        return urldecode('/' . $this->getContainer()->name . '/' . $path);
    }
}
