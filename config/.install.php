<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации установки расширения.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'id'          => 'gm.be.references.media_folders',
    'moduleId'    => 'gm.be.references',
    'name'        => 'Media folder structure',
    'description' => 'Organizing media folders of materials into a structure',
    'namespace'   => 'Gm\Backend\References\MediaFolders',
    'path'        => '/gm/gm.be.references.media_folders',
    'route'       => 'media-folders',
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'info'],
    'events'      => ['gm.be.mediafiles:onDeskView'],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
        ['module', 'id' => 'gm.be.references'],
        ['extension', 'id' => 'gm.be.references.folder_profiles']
    ]
];
