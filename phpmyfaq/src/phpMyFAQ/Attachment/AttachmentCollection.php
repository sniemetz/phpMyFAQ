<?php

/**
 * Attachment collection class.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Anatoliy Belsky <ab@php.net>
 * @copyright 2010-2025 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2010-12-13
 */

namespace phpMyFAQ\Attachment;

use phpMyFAQ\Configuration;
use phpMyFAQ\Database;

/**
 * Class Collection
 *
 * @package phpMyFAQ\Attachment
 */
class AttachmentCollection
{
    /**
     * Constructor.
     */
    public function __construct(protected Configuration $configuration)
    {
    }

    /**
     * Get an array with minimalistic attachment metadata.
     */
    public function getBreadcrumbs(): array
    {
        $breadCrumbs = [];

        $query = sprintf(
            '
            SELECT
                fa.id AS id,
                fa.record_id AS record_id,
                fa.record_lang AS record_lang,
                fa.filename AS filename,
                fa.filesize AS filesize,
                fa.mime_type AS mime_type,
                fd.thema AS thema
            FROM
                %s fa
            JOIN
                %s fd
            ON
                fa.record_id = fd.id
            GROUP BY
                fa.id,fa.record_id,fa.record_lang,fa.filename,fa.filesize,fa.mime_type,fd.thema',
            Database::getTablePrefix() . 'faqattachment',
            Database::getTablePrefix() . 'faqdata'
        );

        $result = $this->configuration->getDb()->query($query);

        if ($result) {
            return $this->configuration->getDb()->fetchAll($result);
        }

        return $breadCrumbs;
    }
}
