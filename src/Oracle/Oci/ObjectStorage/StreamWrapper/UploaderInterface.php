<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Psr\Http\Message\StreamInterface;

/**
 * An interface for classes that can upload objects.
 * If this is created using a class name in the StreamWrapper, the constructor must accept the ObjectStorageClient as single argument.
 */
interface UploaderInterface
{
    /**
     * Upload the stream.
     * @param array $params upload parameters
     * @param StreamInterface $stream stream to upload
     */
    public function upload($params, $stream);
}
