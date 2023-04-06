<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Oracle\Oci\ObjectStorage\ObjectStorageClient;

/**
 * This stream reads using HTTPS. This only works if the bucket is public, so the URL of the object can be constructed.
 *
 * This class does allow streaming and only reading parts of the stream.
 */
class HttpStream extends AbstractHttpStream
{
    /**
     * @param array $params
     * @param ObjectStorageClient $client
     */
    public function __construct($params, $client)
    {
        parent::__construct($params, $client);
    }

    protected function openStream()
    {
        $endpoint = $this->client->getEndpoint();
        $this->fh = fopen($endpoint .
            "/n/" . $this->params[StreamWrapper::NAMESPACE_NAME_PARAM] .
            "/b/" . $this->params[StreamWrapper::BUCKET_NAME_PARAM] .
            "/o/" . $this->params[StreamWrapper::OBJECT_NAME_PARAM], "r");
        return $this->fh;
    }
}
