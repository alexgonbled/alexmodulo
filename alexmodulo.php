<?php
/**
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Alexmodulo extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'alexmodulo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'alex';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('alexmodulo');
        $this->description = $this->l('hola prueba 1 es un modulo de prueba');

        $this->confirmUninstall = $this->l('Estás seguro que quieres desinstalar este modulo???');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided.');
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('ALEXMODULO_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayLeftColumn')&&
            $this->registerHook('displayAdminProductsQuantitiesStepBottom') ;
            
    }

    public function uninstall()
    {
        Configuration::deleteByName('ALEXMODULO_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitAlexmoduloModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAlexmoduloModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'ALEXMODULO_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'ALEXMODULO_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'ALEXMODULO_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    
    protected function getConfigFormValues()
    {
        return array(
            'ALEXMODULO_LIVE_MODE' => Configuration::get('ALEXMODULO_LIVE_MODE', true),
            'ALEXMODULO_ACCOUNT_EMAIL' => Configuration::get('ALEXMODULO_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'ALEXMODULO_ACCOUNT_PASSWORD' => Configuration::get('ALEXMODULO_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }
 
    /*
    Funcion con botón "exportar"
    */



    public function hookDisplayAdminProductsQuantitiesStepBottom($params) {
		$product = new Product($params['id_product']);
        $combination_ids = Product::getProductAttributesIds($params['id_product'], true);
        $combinations= [];
        foreach ($combination_ids as $combination_id){
            $combination= new Combination($combination_id['id_product_attribute']);
            $combinations[]=["Referencia"=> $combination->reference, "Id"=>$combination->id];
        }
        $this->context->smarty->assign("combinaciones",$combinations);
        
        return $this->display(__FILE__, 'hola.tpl');
        
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            
        }
        
        if ($this->context->controller->php_self == 'AdminProducts'){
            $this->context->controller->addJquery();
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            Media::addJsDef(['urlControlador' => $this->context->link->getAdminLink("AdminMoverCombinacionesAOtrasTiendas", true, [], ["ajax" => true])]);

            
    }
}

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayLeftColumn()
    {
        /* Place your code here. */
    }

    public function getPopUp(){
        $id_product = $this->getpro()->id;
        $product = new Product($id_product);
        $this->context->smarty->assign('shops', $this->_path);

    }



public function displayForm()
{
    // Get default language
    $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
    $fieldsForm[0]['form'] = [
        'legend' => [
            'title' => $this->l('Settings'),
        ],
        'input' => [
            [
                'type' => 'text',
                'label' => $this->l('Configuration value'),
                'name' => 'MYMODULE_NAME',
                'size' => 20,
                'required' => true
            ]
        ],
        'submit' => [
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right'
        ]
    ];

    $helper = new HelperForm();

    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    // Language
    $helper->default_form_language = $defaultLang;
    $helper->allow_employee_form_lang = $defaultLang;

    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = [
        'save' => [
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ],
        'back' => [
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        ]
    ];

    // Load current value
    $helper->fields_value['MYMODULE_NAME'] = Tools::getValue('MYMODULE_NAME', Configuration::get('MYMODULE_NAME'));

    
        
    return $helper->generateForm($fieldsForm);
}
}
