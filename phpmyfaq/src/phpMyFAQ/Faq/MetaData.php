<?php

/**
 * FAQ meta-data class for phpMyFAQ.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2020-2025 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2020-11-04
 */

namespace phpMyFAQ\Faq;

use phpMyFAQ\Category;
use phpMyFAQ\Category\Permission;
use phpMyFAQ\Category\Relation;
use phpMyFAQ\Configuration;
use phpMyFAQ\Visits;

/**
 * Class FaqMetaData
 *
 * @package phpMyFAQ\Faq
 */
class MetaData
{
    private ?int $faqId = null;

    private ?string $faqLanguage = null;

    /** @var int[]|null */
    private ?array $categories = null;

    /**
     * FaqPermission constructor.
     */
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function setFaqId(int $faqId): MetaData
    {
        $this->faqId = $faqId;
        return $this;
    }

    public function setFaqLanguage(string $faqLanguage): MetaData
    {
        $this->faqLanguage = $faqLanguage;
        return $this;
    }

    public function setCategories(array $categories): MetaData
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * This method saves the category relations, the initial visits
     * and the permissions
     */
    public function save(): void
    {
        $categoryRelation = new Relation($this->configuration, new Category($this->configuration));
        $categoryRelation->add($this->categories, $this->faqId, $this->faqLanguage);

        // Activate visits
        $visits = new Visits($this->configuration);
        $visits->logViews($this->faqId);

        // Set permissions
        $faqPermission = new Permission($this->configuration);
        $categoryPermission = new Permission($this->configuration);

        $userPermissions = $categoryPermission->get(Permission::USER, $this->categories);

        $faqPermission->add(Permission::USER, $this->categories, $userPermissions);
        $categoryPermission->add(Permission::USER, $this->categories, $userPermissions);

        if ($this->configuration->get('security.permLevel') !== 'basic') {
            $groupPermissions = $categoryPermission->get(Permission::GROUP, $this->categories);
            $faqPermission->add(Permission::GROUP, $this->categories, $groupPermissions);
            $categoryPermission->add(Permission::GROUP, $this->categories, $groupPermissions);
        }
    }
}
