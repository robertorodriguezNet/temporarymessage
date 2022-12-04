<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Temporarymessage extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'temporarymessage';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'Roberto Rodríguez: https://robertorodriguez.net';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Temporary message');
        $this->description = $this->l('Displays a temporary message at the bottom of the page');

        $this->confirmUninstall = $this->l('Are you sure you want uninstall the module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('TEMPORARYMESSAGE_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('TEMPORARYMESSAGE_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');

        $this->context->controller->registerStylesheet(
            'modules-mensaje_temporal',
            'modules/' . $this->name . '/view/css/front.css',
            [
                'media' => 'all',
                'priority' => 100,
            ]
        );

        $this->context->controller->registerJavascript(
            'modules-mensaje_temporal',
            $this->_path . '/views/js/front.js',
            [
                'position' => 'bottom',
                'priority' => 150
            ]
        );
    }

    public function hookDisplayFooter()
    {

        $expire_date = strtotime("11-12-2022 00:00:00");
        $expired = (strtotime(date("d-m-Y H:i:00", time())) >= $expire_date);

        if(!isset($_COOKIE['message_seen']) && !$expired) {
            return $this->display(__FILE__, 'views/templates/hook/' . $this->name . '.tpl');
        } else {
            return '';
        }
    }
}
