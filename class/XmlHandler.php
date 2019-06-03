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

use  XoopsModules\Article;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once dirname(__DIR__) . '/include/vars.php';
require_once dirname(__DIR__) . '/include/functions.parse.php';
xoops_load('xoopslocal');

/*** GENERAL USAGE *********************************************************
 * $xmlHandler =\XoopsModules\Article\Helper::getInstance()->getHandler("Xml", $xoopsModule->getVar("dirname"));
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

//art_parse_class('
class XmlHandler
{
    public function &create($format = 'RSS0.91')
    {
        $xmlfeed = new Article\Xmlfeed($format);

        return $xmlfeed;
    }

    public function display(&$feed, $filename = '', $display = false)
    {
        if (!is_object($feed)) {
            return null;
        }
        if ($display) {
            $filename = empty($filename) ? $feed->filename : $filename;
            $feed->saveFeed($feed->version, $filename);
        } elseif (empty($filename)) {
            $content = $feed->createFeed($feed->version);

            return $content;
        }
    }

    public function insert($feed)
    {
        $xml_data             = [];
        $xml_data['version']  = $feed->version;
        $xml_data['encoding'] = $feed->encoding;
        $xml_data['image']    = $feed->image;
        $xml_data['items']    = $feed->items;

        return $xml_data;
    }

    public function &get(&$feed)
    {
        $xml_data             = [];
        $xml_data['version']  = $feed->version;
        $xml_data['encoding'] = $feed->encoding;
        $xml_data['image']    = $feed->image;
        $xml_data['items']    = $feed->items;

        return $xml_data;
    }
}
//');
