<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\References\MediaFolders\Model;

use Gm;
use Gm\Db\Sql;
use Gm\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка медиапапок.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Model
 * @since 1.0
 */
class MediaFolderCombo extends ComboModel
{
    /**
     * Показывать только родительские узлы.
     * 
     * @var bool
     */
    protected bool $onlyParent = false;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{reference_media_folders}}',
            'primaryKey' => 'id',
            'order'      => ['index' => 'ASC'],
            'searchBy'   => 'name',
            'fields' => [
                ['name']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // добавление записи "показывать только родит-е узлы"
        $this->onlyParent = Gm::$app->request->getQuery('onlyParent', false, 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(Sql\AbstractSql $operator): void
    {
        if ($this->onlyParent) {
            $operator->where(['parent_id' => null]);
        }
        if ($this->search) {
            $operator->where->like($this->dataManager->searchBy, '%' . $this->search . '%');
        }
    }
}
