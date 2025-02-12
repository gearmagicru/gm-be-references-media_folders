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
use Gm\Db\ActiveRecord;
use Gm\Db\Sql\Expression;
use Gm\Helper\AdjacencyList;
use Gm\Backend\References\FolderProfiles\Model\FolderProfile;

/**
 * Активная запись медиапапки.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\References\MediaFolders\Model
 * @since 1.0
 */
class MediaFolder extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{reference_media_folders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'         => 'id',
            'parentId'   => 'parent_id', // идент. родительской папки
            'profileId'  => 'profile_id', // идент. профиля папки
            'alias'      => 'alias', // пседовним
            'count'      => 'count', // количество подпапок
            'index'      => 'index', // порядковый номер
            'name'       => 'name', // название
            'breadcrumb' => 'breadcrumb', // название в навигации
            'path'       => 'path', // локальный путь к папке
            'icon'       => 'icon', // значок средний
            'smallIcon'  => 'icon_small', // значок маленький
            'iconCls'    => 'icon_cls', // класс CSS значка
            'visible'    => 'visible' // видимость
        ];
    }

    /**
     * Проверяет, доступна ли медиапапка.
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return boolval($this->visible);
    }

    /**
     * Возвращает запись по указанному значению первичного ключа.
     * 
     * @see ActiveRecord::selectByPk()
     * 
     * @param mixed $id Идентификатор записи.
     * 
     * @return MediaFolder|null Активная запись при успешном запросе, иначе `null`.
     */
    public function get(mixed $identifier): ?self
    {
        return $this->selectByPk($identifier);
    }

    /**
     * Возвращает медиапапку по указанному псевдониму.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param string $alias Псевдоним.
     * 
     * @return MediaFolder|null Активная запись при успешном запросе, иначе `null`.
     */
    public function getByAlias(string $alias): ?self
    {
        return $this->selectOne(['alias' => $alias]);
    }

    /**
     * Проверяет, является ли указанный путь псевдонимом.
     * 
     * @param string $path Путь, например: '@media/images', 'public/images'.
     * 
     * @return bool
     */
    public static function isAliasPath(string $path): bool
    {
        return $path && $path[0] === '@';
    }

    /**
     * Возвращает медиапапку по указанному пути.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param string $path Путь.
     * @param bool $locate Проверить совпадение пути до самого верхнего уровня (по умолчанию `false`).
     * 
     * @return MediaFolder|null Активная запись при успешном запросе, иначе `null`.
     */
    public function getByPath(string $path, bool $locate = false): ?self
    {
        if ($locate) {
            $sql = 'SELECT *, LOCATE(path, :path) checked, path, LENGTH(path) length'
                . ' FROM ' . $this->tableName() 
                . ' HAVING checked = 1 ORDER BY length DESC'
                . ' LIMIT 1';
            /** @var array|null $row */
            $row = $this->db
                    ->createCommand($sql)
                    ->bindValue(':path', trim($path, '/'))
                        ->queryOne();
            if ($row) {
                $this->reset();
                $this->afterSelect();
                $this->populate($this, $row);
                $this->afterPopulate();
                return $this;
            }
            return null; 
        }
        return $this->selectOne(['path' => $path]);
    }

    /**
     * Возвращает все "корневые" доступные медиапапки (не имеющих родителя).
     * 
     * @return array<string, mixed>
     */
    public function getRoot(): array
    {
        return $this->fetchAll(
            null, 
            ['*'],
            ['visible' => 1, 'parent_id' => null]
        );
    }

    /**
     * Возвращает все доступные медиапапки в виде узлов дерева.
     * 
     * @param string|array|null $where Условие выполнения запроса (по умолчанию `null`).
     * @param mixed 
     * 
     * @return array
     */
    public function getNodes(string|array|null $where = null): array
    {
        /** @var array $nodes */
        $nodes = [];

        /** @var array $folders */
        $folders = $this->fetchAll(null, ['*'], $where);

        $parents = [];
        foreach ($folders as $folder) {
            $parents[$folder['id']] = $folder['alias'];
        }
        foreach ($folders as $folder) {
            $isLeaf = empty($folder['count']);
            $node = [
                'id'        => $folder['alias'],
                'parentId'  => $parents[$folder['parent_id']] ?? '',
                'folderId'  => $folder['id'], // для редактирования медиапапки из панели инструментов
                'profileId' => $folder['profile_id'], // для редактирования профиля медиапапки из панели инструментов
                'text'      => $folder['name'],
                'leaf'      => $isLeaf,
                'iconCls'   => 'x-tree-icon-parent'
            ];
            if ($folder['icon_small']) {
                $node['icon'] = $folder['icon_small'];
            }
            if ($folder['icon_cls']) {
                $node['iconCls'] = $folder['icon_cls'];
            }
            $nodes[] = $node;
        }
        return $nodes;
    }

    /**
     * Возвращает все доступные медиапапки в виде полей с флажками.
     * 
     * @param string|array|null $where Условие выполнения запроса (по умолчанию `null`).
     * @param mixed 
     * 
     * @return array
     */
    public function getTreeRows($callback = null): array
    {
        /** @var array $nodes */
        $nodes = [];

        /** @var array $folders */
        $folders = $this->fetchAll(null, ['*']);

        /** @var array $folderTree Корневые узлы дерева  */
        return AdjacencyList::getTreeRows($folders, $callback);
    }

    /**
     * Возвращает все (дочернии) медиапапки по указанному идентифкатору родителя.
     * 
     * @param bool $visible Только видимые (дочернии) медиапапки (по умолчанию `true`).
     * 
     * @return array
     */
    public function getChildren(bool $visible = true): array
    {
        return $this->fetchAll(
            null, 
            ['*'],
            ['visible' => $visible ? 1 : 0, 'parent_id' => $this->id]
        );
    }

    /**
     * Проверяет, имеет ли медипапка вложенные папки.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->count > 0;
    }

    /**
     * Проверяет, имеет ли медипапка путь.
     *
     * @return bool
     */
    public function hasPath(): bool
    {
        return !empty($this->path);
    }

    /**
     * Возвращает цепочку навигации.
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->fetchAll(
            'alias', 
            ['alias', 'title' => new Expression('IF(LENGTH(breadcrumb),breadcrumb,name)')],
            ['visible' => 1]
        );
    }

    /**
     * @var FolderProfile|null
     */
    protected ?FolderProfile $folderProfile;

    /**
     * Возвращает профиль медиапапки.
     *
     * @return FolderProfile|null
     */
    public function getFolderProfile(): ?FolderProfile
    {
        if (isset($this->folderProfile)) {
            return  $this->folderProfile;
        }

        if ($this->profileId) {
            return $this->folderProfile = Gm::getEModel('FolderProfile', 'gm.be.references.folder_profiles')
                ->get($this->profileId);
        }
        return $this->folderProfile = null;
    }

    /**
     * Проверяет указанное разрешение для медиапапки.
     * 
     * @param string $permission Разрешение, например: 'upload', 'download', 'delete', 
     *     'createFile', 'createFolder', 'compress', 'uncompress', 'rename', 'editFile', 
     *     'viewFile', 'editPerms', 'viewAttr'.
     * @param bool $default Значение по умолчанию, если профиль медиапапки не существует.
     * 
     * @return bool
     */
    public function can(string $permission, bool $default = true): bool
    {
        /** @var FolderProfile|null $profile */
        $profile = $this->getFolderProfile();
        return $profile ? $profile->can($permission) : $default;
    }
}
