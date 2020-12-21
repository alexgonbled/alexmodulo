<?php
class AdminMoverCombinacionesAOtrasTiendasController extends ModuleAdminController {

	public function __construct(){
		$this->ajax = '1';
		parent::__construct();
	}

	public function ajaxProcess() {
        $result = ['success' => true, 'errors' => ["Acción no definida"]];
		$idcombinaciones = Tools::getValue('idcombinaciones');
        foreach ( $idcombinaciones as $idcomb){
        
        $result['success'] = $result['success'] && Db::getInstance()->execute("INSERT IGNORE INTO `"._DB_PREFIX_."product_attribute_shop` SELECT `id_product`,`id_product_attribute`,2,`wholesale_price`,`price`,`ecotax`,`weight`,`unit_price_impact`,`default_on`,`minimal_quantity`,`low_stock_threshold`,`low_stock_alert`,`available_date` FROM `"._DB_PREFIX_."product_attribute_shop`  WHERE `id_product_attribute`=".$idcomb);
        }
        
        //die(json_encode($result));
		
	}

}