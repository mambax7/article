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

include __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_RECON_LOADED', true);

if (!defined('ART_FUNCTIONS_RECON')):
    define('ART_FUNCTIONS_RECON', 1);

    function art_synchronization($type = '')
    {
        switch ($type) {
            case 'article':
            case 'topic':
            case 'category':
                $type  = [$type];
                $clean = [$type];
                break;
            default:
                $type  = null;
                $clean = ['category', 'topic', 'article', 'text', 'rate', 'spotlight', 'pingback', 'trackback'];
                break;
        }
        foreach ($clean as $item) {
            $handler = xoops_getModuleHandler($item, $GLOBALS['artdirname']);
            $handler->cleanOrphan();
            unset($handler);
        }
        /*
         if(empty($type) || in_array("category", $type)):
         $categoryHandler = xoops_getModuleHandler("category", $GLOBALS["artdirname"]);
         $categoryHandler->setLastArticleIds();
         $categoryHandler->updateTrack();
         endif;
         */
        if (empty($type) || in_array('article', $type)):
            $articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
        $artConfig      = art_load_config();
        $articleHandler->cleanExpires($artConfig['article_expire'] * 24 * 3600);
        endif;

        return true;
    }

    /**
     * A very rough function to reconcile article tags
     *
     * Far to complete, like removing tags that have been removed from an article
     * @param int $mid
     * @return bool
     */
    function art_updateTag($mid = 0)
    {
        if (!@require_once XOOPS_ROOT_PATH . '/modules/tag/include/functions.php') {
            return false;
        }
        if (!$tagHandler = tag_getTagHandler()) {
            return false;
        }
        $table_article = art_DB_prefix('article');

        $sql = '    SELECT art_id, art_keywords' . '    FROM ' . art_DB_prefix('article') . '    WHERE art_time_publish >0' . "        AND art_keywords <> '' ";

        if (false === ($result = $GLOBALS['xoopsDB']->query($sql))) {
            //xoops_error($GLOBALS['xoopsDB']->error());
        }
        $mid = empty($mid) ? $GLOBALS['xoopsModule']->getVar('mid') : $mid;
        while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
            if (empty($myrow['art_keywords'])) {
                continue;
            }
            $tagHandler->updateByItem($myrow['art_keywords'], $myrow['art_id'], $mid);
        }

        return true;
    }

endif;
