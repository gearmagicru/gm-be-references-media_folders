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
use Gm\Panel\Data\Model\AdjacencyFormModel;

/**
 * Модель данных профиля медиапапки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Model
 * @since 1.0
 */
class Form extends AdjacencyFormModel
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
            // поля
            'fields' => [
                ['id'],
                [ // порядковый номер
                    'index', 
                    'label' => 'Index'
                ],
                [ // название
                    'name', 
                    'label' => 'Name'
                ],
                [ // локальный путь
                    'path', 
                    'label' => 'Path'
                ],
                [ // идент. профиля медиапапки
                    'profile_id', 
                    'alias' => 'profileId',
                    'label' => 'Folder profile'
                ],
                [ // заголовок
                    'breadcrumb',
                    'label' => 'Breadcrumb',
                ],
                [ // псевдоним
                    'alias', 
                    'label' => 'Alias'
                ],
                [ // идент. родительской папки
                    'parent_id', 
                    'alias' => 'parentId', 
                    'label' => 'Parent folder'
                ],
                [ // значок
                    'icon', 
                    'alias' => 'asIcon', 
                    'label' => 'Medium icon'
                ],
                [ // маденький значок
                    'icon_small', 
                    'alias' => 'asSmallIcon', 
                    'label' => 'Small icon'
                ],
                [ // css класс значка
                    'icon_cls', 
                    'alias' => 'asIconCls', 
                    'label' => 'Icon CSS'
                ],
                [ // видимость
                    'visible', 
                    'label' => 'Visible'
                ]
            ],
            // уникальность значений полей
            'uniqueFields' => ['alias', 'path'],
            // правила форматирования полей
            'formatterRules' => [
                [['name', 'alias', 'breadcrumb', 'icon', 'path'], 'safe'],
                [['visible'], 'logic'],
                [['profileId'], 'combo']
            ],
            // правила валидации полей
            'validationRules' => [
                [['name', 'alias'], 'notEmpty'],
                // порядковый номер
                [
                    'index', 
                    'between',
                    'min' => 1, 'max' => PHP_INT_MAX
                ],
                // название, псевдоним, заголовок, значок, путь
                [
                    ['name', 'alias', 'breadcrumb', 'asIcon', 'asSmallIcon', 'asIconCls', 'path'],
                    'compare',
                    'condition' => '>=length', 'with' => 255
                ]
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
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                /** @var int|string $appendTo Идентификатор родителя */
                if ($isInsert)
                    $appendTo = Gm::$app->request->getQuery('appendTo', 'root', 'int');
                else
                    $appendTo = $this->parentId ?: 'root';

                /** @var \Gm\Panel\Http\Response\JsongMetadata $meta */
                $meta = $this->response()->meta;
                $meta
                    // всплывающие сообщение
                    ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                    // обновить сетку
                    ->cmdReloadTreeGrid($this->module->viewId('grid'), $appendTo);
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Gm\Panel\Http\Response\JsongMetadata $meta */
                $meta = $this->response()->meta;
                $meta
                    // всплывающие сообщение
                    ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                    // обновить сетку
                    ->cmdReloadTreeGrid($this->module->viewId('grid'));
            });
    }

    /**
     * Возвращает значение атрибута "profileId" элементу интерфейса формы.
     * 
     * @param null|string|int $value
     * 
     * @return array
     */
    public function outProfileId($value): array
    {
        /** @var \Gm\Backend\References\FolderProfiles\Model\FolderProfile $folderProfile */
        $profile = $value ? Gm::getEModel('FolderProfile', 'gm.be.references.folder_profiles') : null;
        $profile = $profile ? $profile->selectByPk($value) : null;
        if ($profile) {
            return [
                'type'  => 'combobox', 
                'value' => $profile->id, 
                'text'  => $profile->name
            ];
        }
        return [
            'type'  => 'combobox',
            'value' => 0,
            'text'  => Gm::t(BACKEND, '[None]')
        ]; 
    }
}
