<?php

/**
 * Constants for Elasticsearch support in phpMyFAQ, you can change them for your needs.
 * Please back up this file before updating phpMyFAQ as it will be overwritten.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2015-2025 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2015-12-27
 * @codingStandardsIgnoreFile
 */

/**
 * Number of shards
 */
const PMF_ELASTICSEARCH_NUMBER_SHARDS = 2;

/**
 * Number of replicas
 */
const PMF_ELASTICSEARCH_NUMBER_REPLICAS = 0;

/**
 * Elasticsearch Tokenizer:
 *
 * - default: standard
 *
 * - also possible, if the ICU Analyzer plugin is installed: icu_tokenizer
 */
const PMF_ELASTICSEARCH_TOKENIZER = 'standard';

/**
 * Array of stemmer token filters with the preferred values by Elasticsearch
 *
 * @var array
 */
const PMF_ELASTICSEARCH_STEMMING_LANGUAGE = [
    'ar' => 'arabic',
    'bn' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'bs' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'bg' => 'bulgarian',
    'ca' => 'catalan',
    'cs' => 'czech',
    'cy' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'da' => 'danish',
    'de' => 'light_german',
    'el' => 'greek',
    'en' => 'english',
    'es' => 'light_spanish',
    'eu' => 'basque',
    'fa' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'fi' => 'finnish',
    'fr' => 'light_french',
    'fr_ca' => 'light_french',
    'ga' => 'irish',
    'he' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'hi' => 'hindi',
    'hu' => 'hungarian',
    'id' => 'indonesian',
    'it' => 'light_italian',
    'ja' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'ko' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'lt' => 'lithuanian',
    'lv' => 'latvian',
    'ms' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'nb' => 'norwegian',
    'nl' => 'dutch',
    'pl' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'pt' => 'light_portuguese',
    'pt_br' => 'brazilian',
    'ro' => 'romanian',
    'ru' => 'russian',
    'sk' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'sl' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'sr' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'sv' => 'swedish',
    'th' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'tr' => 'turkish',
    'tw' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'uk' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'vi' => 'english', // NOT SUPPORTED BY ELASTICSEARCH
    'zh' => 'english' // NOT SUPPORTED BY ELASTICSEARCH
];
