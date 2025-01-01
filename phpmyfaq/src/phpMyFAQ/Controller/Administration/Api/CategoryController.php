<?php

/**
 * The Admin Category Controller
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2023-2025 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2023-10-27
 */

namespace phpMyFAQ\Controller\Administration\Api;

use phpMyFAQ\Category;
use phpMyFAQ\Category\Image;
use phpMyFAQ\Category\Order;
use phpMyFAQ\Category\Permission;
use phpMyFAQ\Category\Relation;
use phpMyFAQ\Controller\AbstractController;
use phpMyFAQ\Core\Exception;
use phpMyFAQ\Enums\PermissionType;
use phpMyFAQ\Filter;
use phpMyFAQ\Session\Token;
use phpMyFAQ\Translation;
use phpMyFAQ\User\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('admin/api/category/delete')]
    public function delete(Request $request): JsonResponse
    {
        $this->userHasPermission(PermissionType::CATEGORY_DELETE);

        $currentUser = CurrentUser::getCurrentUser($this->configuration);

        $data = json_decode($request->getContent());

        if (!Token::getInstance($this->container->get('session'))->verifyToken('category', $data->csrfToken)) {
            return $this->json(['error' => Translation::get('msgNoPermission')], Response::HTTP_UNAUTHORIZED);
        }

        [ $currentAdminUser, $currentAdminGroups ] = CurrentUser::getCurrentUserGroupId($currentUser);

        $category = new Category($this->configuration, [], false);
        $category->setUser($currentAdminUser);
        $category->setGroups($currentAdminGroups);

        $categoryRelation = new Relation($this->configuration, $category);

        $categoryImage = new Image($this->configuration);
        $categoryImage->setFileName($category->getCategoryData($data->categoryId)->getImage());

        $categoryOrder = new Order($this->configuration);
        $categoryOrder->remove($data->categoryId);

        $categoryPermission = new Permission($this->configuration);

        if (
            (
                is_countable($category->getCategoryLanguagesTranslated($data->categoryId))
                    ? count($category->getCategoryLanguagesTranslated($data->categoryId)) : 0
            ) === 1
        ) {
            $categoryPermission->delete(Permission::USER, [$data->categoryId]);
            $categoryPermission->delete(Permission::GROUP, [$data->categoryId]);
            $categoryImage->delete();
        }

        if (
            $category->delete($data->categoryId, $data->language) &&
            $categoryRelation->delete($data->categoryId, $data->language)
        ) {
            return $this->json(['success' => Translation::get('ad_categ_deleted')], Response::HTTP_OK);
        } else {
            return $this->json(
                ['error' => Translation::get('ad_adus_dberr') . $this->configuration->getDb()->error()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @throws Exception
     */
    #[Route('admin/api/category/permissions', methods: ['GET'])]
    public function permissions(Request $request): JsonResponse
    {
        $this->userIsAuthenticated();

        $categoryPermission = new Permission($this->configuration);

        $categoryData = $request->get('categories');

        if (empty($categoryData)) {
            $categories = [-1]; // Access for all users and groups
        } else {
            $categories = explode(',', (string) $categoryData);
        }

        if (!in_array(true, filter_var_array($categories, FILTER_VALIDATE_INT))) {
            return $this->json(['error' => 'Only integer values are valid.'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            [
                'user' => $categoryPermission->get(Permission::USER, $categories),
                'group' => $categoryPermission->get(Permission::GROUP, $categories)
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @throws Exception
     */
    #[Route('admin/api/category/translations')]
    public function translations(Request $request): JsonResponse
    {
        $this->userIsAuthenticated();

        $category = new Category($this->configuration, [], false);

        $categoryId = Filter::filterVar($request->get('categoryId'), FILTER_VALIDATE_INT);

        $translations = $category->getCategoryLanguagesTranslated($categoryId);

        return $this->json($translations, Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    #[Route('admin/api/category/update-order')]
    public function updateOrder(Request $request): JsonResponse
    {
        $this->userHasPermission(PermissionType::CATEGORY_EDIT);

        $data = json_decode($request->getContent());

        if (!Token::getInstance($this->container->get('session'))->verifyToken('category', $data->csrfToken)) {
            return $this->json(['error' => Translation::get('msgNoPermission')], Response::HTTP_UNAUTHORIZED);
        }

        $user = CurrentUser::getCurrentUser($this->configuration);

        [ $currentAdminUser, $currentAdminGroups ] = CurrentUser::getCurrentUserGroupId($user);

        $categoryOrder = new Order($this->configuration);
        $categoryOrder->setCategoryTree($data->categoryTree);

        $parentId = $categoryOrder->getParentId($data->categoryTree, (int)$data->categoryId);

        $category = new Category($this->configuration, [], false);
        $category->setUser($currentAdminUser);
        $category->setGroups($currentAdminGroups);
        $category->updateParentCategory($data->categoryId, $parentId);

        return $this->json(['success' => Translation::get('ad_categ_save_order')], Response::HTTP_OK);
    }
}
