<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\References\MediaFolders\Controller;

use Gm;
use Gm\Panel\Controller\FormController;
use Gm\Backend\References\MediaFolders\Model\MediaFolder;
use Gm\Backend\References\MediaFolders\Widget\EditWindow;

/**
 * Контроллер формы структуры медиапапок.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * Атрибуты медиапапки в которую добавляют.
     * 
     * @var array
     */
    protected array $appendTo = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_BEFORE_ACTION, function ($controller, $action, &$result) {
                if ($action === 'view') {
                    /** @var int|null $appendTo */
                    $appendTo = Gm::$app->request->getQuery('appendTo', 0, 'int');
                    if ($appendTo !== 0) {
                        /** @var MediaFolder $mediaFolder */
                        $mediaFolder =  (new MediaFolder)->get((int) $appendTo);
                        if ($mediaFolder === null) {
                            $this->getResponse()
                                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['appendTo']));
                            $result = false;
                            return;
                        }
                        $this->appendTo = $mediaFolder->getAttributes();
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        return new EditWindow(['appendTo' => $this->appendTo]);
    }
}
