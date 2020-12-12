<?php
namespace Edbox\PSModule\EdboxModule\Concerns;

use Exception;
use Language;
use Validate;
use Tools;
use Tab;

/**
 * Provides install, uninstall module tabs methods
 */
trait HasTabs
{
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

        /** @var array (id_lang, iso_code) */
        $languages = Language::getIsoIds();

        $return = true;
        $addIcon = true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=');

        foreach ($tabs as $tabData) {
            $id_parent = !empty($tabData['parent_class_name']) ? (int) Tab::getIdFromClassName($tabData['parent_class_name']) : 0;
            $module = $this->name;
            $class_name = $tabData['class_name'];
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

            // skip creation if already there, or maybe it is better to delete??
            if (Tab::getIdFromClassName($class_name)) {
                $return &= true;

                continue;
            }

            $tab = new Tab();
            $tab->id_parent = $id_parent;
            $tab->module = $module;
            $tab->class_name = $class_name;
            $tab->active = $active;
            $tab->name = $names;

            if ($addIcon) {
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
            if (Validate::isLoadedObject($tab = Tab::getInstanceFromClassName($tabData['class_name']))) {
                $return &= $tab->delete();
            }
        }
        return $return;
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