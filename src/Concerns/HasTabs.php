<?php
namespace Edbox\PSModule\EdboxModule\Concerns;

use Exception;
use Language;
use Validate;
use Tools;
use DbQuery;
use Db;
use Tab;

/**
 * Provides install, uninstall module tabs methods
 */
trait HasTabs
{
    /**
     * Put all tabs inside this tab. If you want to skip this option, than make this property null
     * @var array
     */
    protected $parentTab = [
        'class_name' => 'EdboxModule',
        'parent_class_name' => 0,
        'name' => [
            'en' => 'Edbox Modules',
            'lt' => 'Edbox Moduliai',
        ],
        'module' => '',
        'visible' => true,
    ];

    /**
     * The Core is supposed to register the tabs automatically thanks to the getTabs() return.
     * However in 1.7.5 it only works when the module contains a Admin<legacy-controller>Controller file,
     * this works fine when module has been upgraded and the former file is still present however
     * for a fresh install we need to install it manually until the core is able to manage new modules.
     *
     * @return bool
     */
    public function installTabs()
    {
        /** @var array */
        $tabs = $this->getTabs();

        // if no tabs defined, we say all good nothing to install so no need to trow error
        if (empty($tabs)) {
            return true;
        }

        // prepend parent tab to the beginning of all tabs array
        if (!empty($this->parentTab)) {
            array_unshift($tabs, $this->parentTab);
        }

        /** @var array (id_lang, iso_code) */
        $languages = Language::getIsoIds();

        $return = true;

        foreach ($tabs as $tabData) {
            $class_name = $tabData['class_name'];

            // skip creation if already there
            if (Tab::getIdFromClassName($class_name)) {
                $return &= true;

                continue;
            }

            $id_parent = !empty($tabData['parent_class_name']) ? (int) Tab::getIdFromClassName($tabData['parent_class_name']) : 0;
            $module = isset($tabData['module']) ? $tabData['module'] : $this->name;
            $icon = !empty($tabData['icon']) ? $tabData['icon'] : '';
            $active = isset($tabData['visible']) ? (int) $tabData['visible'] : 0;

            $name = !empty($tabData['name']) ? $tabData['name'] : '';
            $this->validateName($name);

            $names = [];
            $isArrayNames = is_array($name);

            foreach ($languages as $lang) {

                if ($isArrayNames) {
                    $allnames = array_values($name);
                    $firstName = array_shift($allnames);

                    $names[$lang['id_lang']] = !empty($name[$lang['iso_code']])
                        ? $name[$lang['iso_code']]
                        : $firstName; // get first array value

                } else {
                    $names[$lang['id_lang']] = $name;
                }

                $this->validateName($names[$lang['id_lang']]);
            }

            $tab = new Tab();
            $tab->id_parent = $id_parent;
            $tab->module = $module;
            $tab->class_name = $class_name;
            $tab->active = $active;
            $tab->name = $names;
            if (property_exists($tab, 'icon')) {
                $tab->icon = $icon;
            }

            $return &= $tab->add();
        }

        return $return;
    }

    /**
     * Remove tabs
     *
     * @return boolean
     */
    public function removeTabs()
    {
        /** @var array */
        $tabs = $this->getTabs();
        $return = true;

        foreach ($tabs as $tabData) {
            $return &= $this->removeTabByClassName($tabData['class_name']);
        }

        // remove parent tab if no tab is using it as a parent tab
        if (!$this->parentTabHasSubTabs()) {
            $return &= $this->removeTabByClassName($this->parentTab['class_name']);
        }

        return $return;
    }

    /**
     * Check if parent tab has sub tabs
     *
     * @return boolean
     */
    private function parentTabHasSubTabs()
    {
        if (empty($this->parentTab['class_name'])) {
            return;
        }

        $className = $this->parentTab['class_name'];
        $id = Tab::getIdFromClassName($className);

        if (empty($id)) {
            return;
        }

        $query = new DbQuery();
        $query->select('count(*)');
        $query->from('tab');
        $query->where('id_parent = ' . (int) $id);

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) > 0;
    }

    /**
     * Remove tab by class name
     *
     * @param  string $className
     *
     * @return boolean
     */
    private function removeTabByClassName($className)
    {
        if (Validate::isLoadedObject($tab = Tab::getInstanceFromClassName($className))) {
            return $tab->delete();
        }
        return true;
    }

    /**
     * Check if name is not empty string
     *
     * @param  string $name
     *
     * @return void
     *
     * @throws Exception
     */
    private function validateName($name)
    {
        if (!$name) {
            throw new Exception('Module tabs array must contain defined name');
        }
    }
}