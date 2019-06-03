<?php

namespace XoopsModules\Article;

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
require_once dirname(__DIR__) . '/include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

//art_parse_class('
class XmlrpcHandler
{
    public function &get($type = 'c')
    {
        switch (mb_strtolower($type)) {
            case 's':
            case 'server':
                return new Xmlrpc_server();
            case 'c':
            case 'client':
                return new \Xmlrpc_client();
        }
    }

    public function display(&$feed, $filename = '')
    {
        if (!is_object($feed)) {
            return null;
        }
        $filename = empty($filename) ? $feed->filename : $filename;
        echo $feed->saveFeed($feed->version, $filename);
    }

    public function utf8_encode(&$feed)
    {
        if (!is_object($feed)) {
            return null;
        }
        $text = xoops_utf8_encode(serialize($feed));
        $feed = unserialize($text);
    }
}
//');
