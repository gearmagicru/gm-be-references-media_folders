<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

/** @var bool $isSetup Если установщик приложения  */
$isSetup = $this->getParam('isSetup', false);
/** @var bool $isRu Если язык установки русский */
$isRu = $this->getParam('isRu', false);
/** @var string $baseUrl */
$baseUrl = MODULE_BASE_URL . '/gm/gm.be.references.media_folders/assets/images/folders';

return [
    'drop'   => ['{{reference_media_folders}}'],
    'create' => [
        '{{reference_media_folders}}' => function () {
            return "CREATE TABLE `{{reference_media_folders}}` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) unsigned DEFAULT NULL,
                `profile_id` int(11) unsigned DEFAULT NULL,
                `alias` varchar(255) DEFAULT NULL,
                `count` int(11) unsigned DEFAULT NULL,
                `index` int(11) unsigned DEFAULT 1,
                `name` varchar(255) DEFAULT NULL,
                `breadcrumb` varchar(255) DEFAULT NULL,
                `icon` varchar(255) DEFAULT NULL,
                `icon_small` varchar(255) DEFAULT NULL,
                `icon_cls` varchar(100) DEFAULT NULL,
                `path` varchar(255) DEFAULT NULL,
                `visible` tinyint(1) unsigned DEFAULT 1,
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],

    'insert' => [
        '{{reference_media_folders}}' => [
            [
                'id'         => 1,
                'parent_id'  => null,
                'profile_id' => $isSetup ? 1 : null, // общий
                'index'      => 1,
                'name'       => $isRu ? 'Медиафайлы' : 'Media files',
                'breadcrumb' => $isRu ? 'Медиафайлы' : 'Media files',
                'alias'      => '@media',
                'icon_cls'   => 'gm-references-mediafolders__folder-public',
                'visible'    => 1,
                'count'      => 4
            ],
                [
                    'id'         => 2,
                    'parent_id'  => 1,
                    'profile_id' => null, // общии
                    'index'      => 1,
                    'name'       => $isRu ? 'Общии' : 'Common',
                    'breadcrumb' => $isRu ? 'Общии' : 'Common',
                    'alias'      => '@media/common',
                    'visible'    => 1,
                    'count'      => 2
                ],
                    [
                        'id'         => 3,
                        'parent_id'  => 2,
                        'profile_id' => $isSetup ? 2 : null, // общии изображения
                        'index'      => 1,
                        'name'       => $isRu ? 'Изображения' : 'Images',
                        'breadcrumb' => $isRu ? 'Изображения' : 'Images',
                        'alias'      => '@media/common/images',
                        'path'       => 'public/uploads/img',
                        'icon'       => $baseUrl . '/icon-folder_images_thumb.svg',
                        'icon_cls'   => 'gm-references-mediafolders__folder-images',
                        'visible'    => 1
                    ],
                    [
                        'id'         => 4,
                        'parent_id'  => 2,
                        'profile_id' => $isSetup ? 3 : null, // общии документы
                        'index'      => 2,
                        'name'       => $isRu ? 'Документы' : 'Documents',
                        'breadcrumb' => $isRu ? 'Документы' : 'Documents',
                        'alias'      => '@media/common/documents',
                        'path'       => 'public/uploads/doc',
                        'icon'       => $baseUrl . '/icon-folder_docs_thumb.svg',
                        'icon_cls'   => 'gm-references-mediafolders__folder-docs',
                        'visible'    => 1
                    ],
                [
                    'id'         => 5,
                    'parent_id'  => 1,
                    'profile_id' => $isSetup ? 4 : null, // изображения материала
                    'index'      => 1,
                    'name'       => $isRu ? 'Изображения' : 'Images',
                    'breadcrumb' => $isRu ? 'Изображения' : 'Images',
                    'alias'      => '@media/images',
                    'path'       => 'public/uploads/i',
                    'icon'       => $baseUrl . '/icon-folder_images_thumb.svg',
                    'icon_cls'   => 'gm-references-mediafolders__folder-images',
                    'visible'    => 1
                ],
                [
                    'id'         => 6,
                    'parent_id'  => 1,
                    'profile_id' => $isSetup ? 5 : null, // видео материала
                    'index'      => 2,
                    'name'       => $isRu ? 'Видео' : 'Video',
                    'breadcrumb' => $isRu ? 'Видео' : 'Video',
                    'alias'      => '@media/video',
                    'path'       => 'public/uploads/v',
                    'icon'       => $baseUrl . '/icon-folder_video_thumb.svg',
                    'icon_cls'   => 'gm-references-mediafolders__folder-video',
                    'visible'    => 1
                ],
                [
                    'id'         => 7,
                    'parent_id'  => 1,
                    'profile_id' => $isSetup ? 6 : null, // аудио материала
                    'index'      => 3,
                    'name'       => $isRu ? 'Аудио' : 'Audio',
                    'breadcrumb' => $isRu ? 'Аудио' : 'Audio',
                    'alias'      => '@media/audio',
                    'path'       => 'public/uploads/a',
                    'icon'       => $baseUrl . '/icon-folder_audio_thumb.svg',
                    'icon_cls'   => 'gm-references-mediafolders__folder-audio',
                    'visible'    => 1
                ],
                [
                    'id'         => 8,
                    'parent_id'  => 1,
                    'profile_id' => $isSetup ? 7 : null, // документ материала
                    'index'      => 4,
                    'name'       => $isRu ? 'Документы' : 'Documents',
                    'breadcrumb' => $isRu ? 'Документы' : 'Documents',
                    'alias'      => '@media/documents',
                    'path'       => 'public/uploads/d',
                    'icon'       => $baseUrl . '/icon-folder_docs_thumb.svg',
                    'icon_cls'   => 'gm-references-mediafolders__folder-docs',
                    'visible'    => 1
                ]
        ]
    ],

    'run' => [
        'install'   => ['drop', 'create', 'insert'],
        'uninstall' => ['drop']
    ]
];