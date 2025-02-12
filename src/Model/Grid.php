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
use Gm\Panel\Data\Model\AdjacencyGridModel;

/**
 * Модель данных сетки структуры медиапапок.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Model
 * @since 1.0
 */
class Grid extends AdjacencyGridModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => false,
            'tableName'  => '{{reference_media_folders}}',
            'primaryKey' => 'id',
            'parentKey'  => 'parent_id',
            'countKey'   => 'count',
            'fields'     => [
                [
                    'parent_id',
                    'alias' => 'parentId'
                ],
                [
                    'index',
                    'alias' => 'asIndex'
                ],
                ['name'],
                ['path'],
                [
                    'icon_small',
                    'alias' => 'icon'
                ],
                [
                    'icon_cls',
                    'alias' => 'iconCls'
                ],
                ['alias'],
                ['count'],
                ['profileName'],
                [
                    'visible',
                    'alias' => 'isVisible'
                ]
            ],
            'order' => [
                'index' => 'ASC'
            ],
            'resetIncrements' => ['{{reference_media_folders}}'],
            'filter' => [
                'parentId' => ['operator' => '=']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                $this->response()
                    ->meta
                        // всплывающие сообщение
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                        // обновить дерево
                        ->cmdReloadTreeGrid($this->module->viewId('grid'));
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                 $this->response()
                    ->meta
                        // обновить дерево
                        ->cmdReloadTreeGrid($this->module->viewId('grid'), 'root');
            });
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(array &$row): void
    {
        // заголовок контекстного меню записи
        $row['popupMenuTitle'] = $row['name'];
        $row['popupMenuItems'] = [
            [3, empty($row['folder']) ? 'disabled' : 'enabled'], // просмотреть
        ];
        //$row['iconCls'] = 'ffffffffffffffff';
    }

    /**
     * {@inheritdoc}
     */
    public function selectNodes(string|int $parentId = null): array
    {
        /** @var \Gm\Db\Sql\Select $select */
        $select = new Sql\Select();
        $select
            ->from(['folders' => $this->dataManager->tableName])
            ->columns(['*'], true)
            ->quantifier(new Sql\Expression('SQL_CALC_FOUND_ROWS'))
            ->join(
                ['profiles' => '{{reference_folder_profiles}}'],
                'profiles.id = folders.profile_id',
                ['profileName' => 'name'],
                Sql\Select::JOIN_LEFT
            );

        // если не задействован фильтр
        if (!$this->hasFilter()) {
            // все дочернии элементы
            $select->where([$this->parentKey() => $parentId]);
        }

        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $this->buildQuery($select);
        $rows    = $this->fetchRows($command);
        $rows    = $this->afterFetchRows($rows);
        return $this->afterSelect($rows, $command);
    }
}
