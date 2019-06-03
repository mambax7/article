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

require_once __DIR__ . '/vars.php';
define($GLOBALS['artdirname'] . '_FUNCTIONS_AUTHOR_LOADED', true);

if (!defined('ART_FUNCTIONS_AUTHOR')):
    define('ART_FUNCTIONS_AUTHOR', 1);

    /**
     * Function to a list of user names associated with their user IDs
     * @param      $userid
     * @param int  $usereal
     * @param bool $linked
     * @return array
     */
    function &art_getAuthorNameFromId($userid, $usereal = 0, $linked = false)
    {
        if (!is_array($userid)) {
            $userid = [$userid];
        }
        xoops_load('XoopsUserUtility');
        $users = \XoopsUserUtility::getUnameFromIds($userid, $usereal);

        if (!empty($linked)) {
            mod_loadFunctions('url', $GLOBALS['artdirname']);
            foreach (array_keys($users) as $uid) {
                $users[$uid] = '<a href="' . art_buildUrl(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.author.php', ['uid' => $uid]) . '">' . $users[$uid] . '</a>';
            }
        }

        return $users;
    }

    function &art_getWriterNameFromIds($writer_ids, $linked = false)
    {
        if (!is_array($writer_ids)) {
            $writer_ids = [$writer_ids];
        }
        $userid = array_map('intval', array_filter($writer_ids));

        $myts  = \MyTextSanitizer::getInstance();
        $users = [];
        if (count($userid) > 0) {
            $sql = 'SELECT writer_id, writer_name FROM ' . art_DB_prefix('writer') . ' WHERE writer_id IN(' . implode(',', array_unique($userid)) . ')';
            if (!$result = $GLOBALS['xoopsDB']->query($sql)) {
                //xoops_error("writer query error: " . $sql);
                return $users;
            }
            mod_loadFunctions('url', $GLOBALS['artdirname']);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $uid         = $row['writer_id'];
                $users[$uid] = $myts->htmlSpecialChars($row['writer_name']);
                if ($linked) {
                    $users[$uid] = '<a href="' . art_buildUrl(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.writer.php', ['writer' => $uid]) . '">' . $users[$uid] . '</a>';
                }
            }
        }

        return $users;
    }

endif;
