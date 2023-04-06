<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
class UploadManagerConstants
{
    // Default part size is set to be 5MB
    const DEFAULT_PART_SIZE_IN_BYTES = 5 * 1024 * 1024;
    // The config key for part size
    const PART_SIZE_IN_BYTES = 'partSizeInBytes';
    // The config key for allowing multipart upload, true|false
    const ALLOW_MULTIPART_UPLOADS = 'allowMultipartUploads';
    // The config key for allowing parallel upload, this will use guzzle each promise to perform parallel upload, true|false
    const ALLOW_PARALLEL_UPLOADS = 'allowParallelUploads';
    // The config key for allowing total number of promise running at the same time
    const CONCURRENCY = 'concurrency';
    // Total number of promises in any given time
    const DEFAULT_CONCURRENCY = 5;

    const DEFAULT_CONFIG = [
        SELF::PART_SIZE_IN_BYTES => self::DEFAULT_PART_SIZE_IN_BYTES,
        SELF::ALLOW_MULTIPART_UPLOADS => true,
        SELF::ALLOW_PARALLEL_UPLOADS => true,
        SELF::CONCURRENCY => self::DEFAULT_CONCURRENCY
    ];
}
