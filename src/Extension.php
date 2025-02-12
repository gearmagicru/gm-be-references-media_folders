<?php
/**
 * Расширение модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\References\MediaFolders;

/**
 * Расширение "Структура медиапапок".
 * 
 * Организация медиапапок материалов в структуру.
 * 
 * Расширение принадлежит модулю "Справочники".
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders
 * @since 1.0
 */
class Extension extends \Gm\Panel\Extension\Extension
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.references.media_folders';

    /**
     * {@inheritdoc}
     */
    public string $defaultController = 'grid';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        /*$this->on('gm.be.mediafiles:onDeskView', function ($module, $widget) {
            $widget->addCss('1111KKKKKKKKKKKKKKK.css');
            //die('oooooooooooooooo');
        });*/
    }
}