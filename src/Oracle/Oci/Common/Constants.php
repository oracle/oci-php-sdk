<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

class Constants
{
    const PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME = "x-obmcs-internal-signing-strategy-name";
    const DATE_HEADER_NAME = "date";
    const REQUEST_TARGET_HEADER_NAME = "(request-target)";
    const HOST_HEADER_NAME = "host";
    const CONTENT_LENGTH_HEADER_NAME = "Content-Length";
    const CONTENT_TYPE_HEADER_NAME = "content-type";
    const X_CONTENT_SHA256_HEADER_NAME = "x-content-sha256";
    const X_CROSS_TENANCY_REQUEST_HEADER_NAME = "x-cross-tenancy-request";
    const X_SUBSCRIPTION_HEADER_NAME = "x-subscription";
    const OPC_OBO_TOKEN_HEADER_NAME = "opc-obo-token";
    const AUTHORIZATION_HEADER_NAME = "Authorization";
    const OPC_WORK_REQUEST_ID_HEADER_NAME = "opc-work-request-id";
}
