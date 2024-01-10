<?php

/**
 * Private phpMyFAQ Admin API: handling of REST calls for the dashboard
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2020-2023 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2020-10-24
 */

use phpMyFAQ\Api;
use phpMyFAQ\Configuration;
use phpMyFAQ\Filter;
use phpMyFAQ\Session;
use phpMyFAQ\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

if (!defined('IS_VALID_PHPMYFAQ')) {
    http_response_code(400);
    exit();
}

//
// Create Request & Response
//
$response = new JsonResponse();
$request = Request::createFromGlobals();

$faqConfig = Configuration::getConfigurationInstance();

$ajaxAction = Filter::filterVar($request->query->get('ajaxaction'), FILTER_SANITIZE_SPECIAL_CHARS);

switch ($ajaxAction) {
    case 'user-visits-last-30-days':
        if ($faqConfig->get('main.enableUserTracking')) {
            $session = new Session($faqConfig);
            $response->setStatusCode(Response::HTTP_OK);
            $response->setData($session->getLast30DaysVisits());
        }
        break;

    case 'version':
        $api = new Api($faqConfig);
        try {
            $versions = $api->getVersions();
            $response->setStatusCode(Response::HTTP_OK);
            if (-1 === version_compare($versions['installed'], $versions['current'])) {
                $response->setData(
                    ['info' => Translation::get('ad_you_should_update')]
                );
            } else {
                $response->setData(
                    ['success' => Translation::get('ad_xmlrpc_latest') . ': phpMyFAQ ' . $versions['current']]
                );
            }
        } catch (DecodingExceptionInterface | TransportExceptionInterface | Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_GATEWAY);
            $response->setData(['error' => $e->getMessage()]);
        }
        break;
}

$response->send();
