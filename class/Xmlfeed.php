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
require_once dirname(__DIR__) . '/include/functions.parse.php';
xoops_load('xoopslocal');

/*** GENERAL USAGE *********************************************************
 * $xmlHandler = \XoopsModules\Article\Helper::getInstance()->getHandler("Xml", $xoopsModule->getVar("dirname"));
 * $xml = $xmlHandler->create("RSS0.91");
 * $xml->setVar("title", $title);
 * $xml->setVar("description", $description);
 * $xml->setVar("descriptionHtmlSyndicated", true);
 * $xml->setVar("link", $link);
 * $xml->setVar("syndicationURL", $syndicationURL);
 *
 * $image = array(
 * "width" => $imagewidth,
 * "height" => $height,
 * "title" => $imagetitle,
 * "url" => $imageurl,
 * "link" => $imagelink,
 * "description" => $imagedesc
 * );
 *
 * $item = array(
 * "title" => $datatitle,
 * "link" => $dataurl,
 * "description" => $datadesc,
 * "descriptionHtmlSyndicated" => true,
 * "date" => $datadate,
 * "source" => $datasource,
 * "author" => $dataauthor
 * );
 *
 * $xml->setImage($image);
 * $xml->addItem($item);
 *
 * $xmlHandler->display($xml);
 */

// your local timezone, set to "" to disable or for GMT
// To be used in feedcreator.class.php
$server_TZ = abs((int)($GLOBALS['xoopsConfig']['server_TZ'] * 3600.0));
$prefix    = ($GLOBALS['xoopsConfig']['server_TZ'] < 0) ? '-' : '+';
$TIME_ZONE = $prefix . date('H:i', $server_TZ);
define('TIME_ZONE', $TIME_ZONE);

// Version string.
define('FEEDCREATOR_VERSION', 'ARTICLE @ XOOPS powered by FeedCreator');

require_once __DIR__ . '/feedcreator.class.php';

/**
 * Description
 *
 * @param type $var description
 * @return type description
 * @link
 */
if (!class_exists('Xmlfeed')) {
    class Xmlfeed extends \UniversalFeedCreator
    {
        public $version;
        public $filename = '';

        public function __construct($version)
        {
            $this->filename = XOOPS_CACHE_PATH . "/article.feed.{$version}.xml";
            $this->version  = $version;
        }

        public function setVar($var, $val, $encoding = false)
        {
            if (!empty($encoding)) {
                $val = $this->convert_encoding($val);
            }
            $this->$var = $val;
        }

        public function convert_encoding($val)
        {
            if (is_array($val)) {
                foreach (array_keys($val) as $key) {
                    $val[$key] = $this->convert_encoding($val[$key]);
                }
            } else {
                $val = \XoopsLocal::convert_encoding($val, $this->encoding, _CHARSET);
            }

            return $val;
        }

        public function getVar($var)
        {
            return $this->$var;
        }

        public function setImage(&$img)
        {
            $image = new \FeedImage();
            foreach ($img as $key => $val) {
                $image->$key = $this->convert_encoding($val);
            }
            $this->setVar('image', $image);
        }

        public function _addItem(&$itm)
        {
            $item = new \FeedItem();
            foreach ($itm as $key => $val) {
                $item->$key = $this->convert_encoding($val);
            }
            $this->addItem($item);
        }

        public function addItems(&$items)
        {
            if (!is_array($items) || 0 == count($items)) {
                return;
            }
            foreach ($items as $item) {
                $this->_addItem($item);
            }
        }
    }
}

