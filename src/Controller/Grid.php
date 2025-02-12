<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\References\MediaFolders\Controller;

use Gm\Panel\Controller\TreeGridController;
use Gm\Backend\References\MediaFolders\Widget\TabTreeGrid;

/**
 *  Контроллер сетки структуры медиапапок.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Controller
 * @since 1.0
 */
class Grid extends TreeGridController
{
    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabTreeGrid
    {
        return new TabTreeGrid();
    }
}
