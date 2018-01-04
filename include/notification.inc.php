<?php
/**
 * Article module for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         article
 * @since           1.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

include __DIR__ . '/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

art_parse_function('
function [VAR_PREFIX]_notify_iteminfo($category, $item_id)
{
    // The $item is not used !

    $item_id = (int)($item_id);
    art_define_url_delimiter();

    switch ($category) {
    case "category":
        $categoryHandler = xoops_getModuleHandler("category", $GLOBALS["artdirname"]);
        $category_obj = categoryHandler->get($item_id);
        if (!is_object($category_obj)) {
            redirect_header(XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/index.php", 2, art_constant("MD_NOACCESS"));

        }
        $item["name"] = $category_obj->getVar("cat_title");
        $item["url"] = XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/view.category.php" . URL_DELIMITER . $item_id;
        break;
    case "article":
        $articleHandler = xoops_getModuleHandler("article", $GLOBALS["artdirname"]);
        $article_obj = articleHandler->get($item_id);
        if (!is_object($article_obj)) {
            redirect_header(XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/index.php", 2, art_constant("MD_NOACCESS"));

        }
        $item["name"] = $article_obj->getVar("art_title");
        $item["url"] = XOOPS_URL . "/modules/" . $GLOBALS["artdirname"] . "/view.article.php" . URL_DELIMITER . $item_id;
        break;
    case "global":
    default:
        $item["name"] = "";
        $item["url"] = "";
        break;
    }

    return $item;
}
');
