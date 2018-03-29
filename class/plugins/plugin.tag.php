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

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Get item fileds:
 * title
 * content
 * time
 * link
 * uid
 * uname
 * tags
 *
 * @var array $items associative array of items: [modid][catid][itemid]
 *
 * @return boolean
 *
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

include __DIR__ . '/vars.php';

if (!function_exists($GLOBALS['artdirname'] . '_tag_iteminfo')):

    mod_loadFunctions('parse', $GLOBALS['artdirname']);

    art_parse_function('
function [DIRNAME]_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
            // catid is not used in article, so just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In article, the item_id is "art_id"
            $items_id[] = (int)($item_id);
        }
    }
    $itemHandler = xoops_getModuleHandler("article", $GLOBALS["artdirname"]);
    $items_obj = $itemHandler->getObjects(new \Criteria("art_id", "(" . implode(", ", $items_id) . ")", "IN"), true);
    art_define_url_delimiter();

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (!$item_obj = items_obj[$item_id]) {
                continue;
            }
            $items[$cat_id][$item_id] = array(
                "title"        => $item_obj->getVar("art_title"),
                "uid"        => $item_obj->getVar("uid"),
                "link"        => "view.article.php" . URL_DELIMITER . "article={$item_id}",
                "time"        => $item_obj->getVar("art_time_publish"),
                "tags"        => tag_parse_tag($item_obj->getVar("art_keywords", "n")),
                "content"    => "",
                );
        }
    }
    unset($items_obj);
}

/**
 * Remove orphan tag-item links
 *
 * @return    boolean
 *
 */
function [DIRNAME]_tag_synchronization($mid)
{
    $itemHandler = xoops_getModuleHandler("article", $GLOBALS["artdirname"]);
    $linkHandler = xoops_getModuleHandler("link", "tag");

    /* clear tag-item links */
    if (version_compare( mysqli_get_server_info(), "4.1.0", "ge" )):
    $sql =  "    DELETE FROM {$linkHandler->table}" .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( tag_itemid NOT IN " .
            "            ( SELECT DISTINCT {$itemHandler->keyName} " .
            "                FROM {$itemHandler->table} " .
            "                WHERE {$itemHandler->table}.art_time_publish > 0" .
            "            ) " .
            "        )";
    else:
    $sql =     "    DELETE {$linkHandler->table} FROM {$linkHandler->table}" .
            "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} " .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( aa.{$itemHandler->keyName} IS NULL" .
            "            OR aa.art_time_publish < 1" .
            "        )";
    endif;
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
      }
}
');
endif;
