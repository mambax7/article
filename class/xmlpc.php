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
require_once __DIR__ . '/../include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

if (!class_exists('Xmlrpc_client')) {
    class Xmlrpc_client
    {
        public function __construct()
        {
        }

        public function setObject(&$article)
        {
            $this->$var = $val;
        }

        public function setVar($var, $val)
        {
            $this->$var = $val;
        }

        public function getVar($var)
        {
            return $this->$var;
        }
    }
}

if (!class_exists('Xmlrpc_server')) {
    class Xmlrpc_server
    {
        public function __construct()
        {
        }

        public function setVar($var, $val)
        {
            $this->$var = $val;
        }

        public function getVar($var)
        {
            return $this->$var;
        }
    }
}

art_parse_class('
class [CLASS_PREFIX]XmlrpcHandler
{
    function &get($type = "c")
    {
        switch (strtolower($type)) {
        case "s":
        case "server":
            return new Xmlrpc_server();
        case "c":
        case "client":
            return new Xmlrpc_client();
        }
    }

    function display(&$feed, $filename = "")
    {
        if (!is_object($feed)) return null;
        $filename = empty($filename) ? $feed->filename : $filename;
        echo $feed->saveFeed($feed->version, $filename);
    }

    function utf8_encode(&$feed)
    {
        if (!is_object($feed)) return null;
        $text = xoops_utf8_encode(serialize($feed));
        $feed = unserialize($text);
    }
}
');
