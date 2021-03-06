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

require_once XOOPS_ROOT_PATH . '/class/uploader.php';

class Uploader extends \XoopsMediaUploader
{
    public $ext                 = '';
    public $ImageSizeCheck      = false;
    public $FileSizeCheck       = false;
    public $CheckMediaTypeByExt = true;

    /**
     * No admin check for uploads
     * @param mixed $uploadDir
     * @param mixed $allowedMimeTypes
     * @param mixed $maxFileSize
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     */

    /**
     * Constructor
     *
     * @param string     $uploadDir
     * @param array|bool $allowedMimeTypes
     * @param int        $maxFileSize
     * @param int        $maxWidth
     * @param int        $maxHeight
     * @internal param int $cmodvalue
     */
    public function __construct(
        $uploadDir,
        $allowedMimeTypes = false,
        $maxFileSize = 0,
        $maxWidth = 0,
        $maxHeight = 0)
    {
        if (!is_array($allowedMimeTypes)) {
            if (false === $allowedMimeTypes || '*' === $allowedMimeTypes) {
                $allowedMimeTypes = false;
            } else {
                $allowedMimeTypes = explode('|', mb_strtolower($allowedMimeTypes));
                if (in_array('*', $allowedMimeTypes)) {
                    $allowedMimeTypes = false;
                }
            }
        }
        parent::__construct($uploadDir, $allowedMimeTypes, $maxFileSize, $maxWidth, $maxHeight);
        //$this->setTargetFileName($this->getMediaName());
    }

    /**
     * Set the CheckMediaTypeByExt
     *
     * @param bool|string $value
     */
    public function setCheckMediaTypeByExt($value = true)
    {
        $this->CheckMediaTypeByExt = $value;
    }

    /**
     * Set the imageSizeCheck
     *
     * @param string $value
     */
    public function setImageSizeCheck($value)
    {
        $this->ImageSizeCheck = $value;
    }

    /**
     * Set the fileSizeCheck
     *
     * @param string $value
     */
    public function setFileSizeCheck($value)
    {
        $this->FileSizeCheck = $value;
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    public function getExt()
    {
        $this->ext = mb_strtolower(ltrim(mb_strrchr($this->getMediaName(), '.'), '.'));

        return $this->ext;
    }

    /**
     * Is the file the right size?
     *
     * @return bool
     */
    public function checkMaxFileSize()
    {
        if (!$this->FileSizeCheck) {
            return true;
        }
        if ($this->mediaSize > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return bool
     */
    public function checkMaxWidth()
    {
        if (!$this->ImageSizeCheck) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[0] > $this->maxWidth) {
                return false;
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max width check..', $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
    }

    /**
     * Is the picture the right height?
     *
     * @return bool
     */
    public function checkMaxHeight()
    {
        if (!$this->ImageSizeCheck) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[1] > $this->maxHeight) {
                return false;
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max height check..', $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
    }

    /**
     * Is the file the right Mime type
     *
     * (is there a right type of mime? ;-)
     *
     * @return bool
     */
    public function checkMimeType()
    {
        if ($this->CheckMediaTypeByExt) {
            $type = $this->getExt();
        } else {
            $type = $this->mediaType;
        }
        if (count($this->allowedMimeTypes) > 0 && !in_array($type, $this->allowedMimeTypes)) {
            return false;
        }

        return true;
    }
}

