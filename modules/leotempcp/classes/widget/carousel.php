<?php 
/******************************************************
 *  Leo Prestashop Theme Framework for Prestashop 1.5.x
 *
 * @package   leotempcp
 * @version   3.0
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 * ******************************************************/

class LeoWidgetCarousel extends LeoWidgetBase {

		public $name = 'Carousel';
		public $for_module = 'manage';
		
		public function getWidgetInfo(){
			return  array('label' => $this->l('Product List Carousel'), 'explain' =>$this->l('Create Products List Carousel !')) ;
		}

		public function renderForm( $args, $data ){
			$helper = $this->getFormHelper();
			$types = array();	
		 	$types[] = array(
		 		'value' => 'newest',
		 		'text'  => $this->l('Products Newest')
		 	);
		 	$types[] = array(
		 		'value' => 'bestseller',
		 		'text'  => $this->l('Products Bestseller')
		 	);

		 	$types[] = array(
		 		'value' => 'special',
		 		'text'  => $this->l('Products Special')
		 	);

		 	$types[] = array(
		 		'value' => 'featured',
		 		'text'  => $this->l('Products Featured')
		 	);
			
			$source = array(
			  array(
				'value' => 'pcategories',                 // The value of the 'value' attribute of the <option> tag.
				'name' 	=> 	$this->l('Category')             // The value of the text content of the  <option> tag.
			  ),
			  array(
				'value' => 'ptype',
				'name' 	=> $this->l('Product')
			  ),
			  array(
				'value' => 'pmanufacturers',
				'name' 	=> $this->l('Manufacturers')
			  ),
			  array(
				'value' => 'pproductids',
				'name' 	=> $this->l('Product Ids')
			  ));		
			
			$orderby = array(
			  array(
				'order' => 'date_add',                 // The value of the 'value' attribute of the <option> tag.
				'name' 	=> 	$this->l('Date Add')             // The value of the text content of the  <option> tag.
			  ),
			  array(
				'order' => 'date_upd',                 // The value of the 'value' attribute of the <option> tag.
				'name' 	=> 	$this->l('Date Update')             // The value of the text content of the  <option> tag.
			  ),
			  array(
				'order' => 'name',
				'name' 	=> $this->l('Name')
			  ),
			  array(
				'order' => 'id_product',
				'name' 	=> $this->l('Product Id')
			  ),
			  array(
				'order' => 'price',
				'name' 	=> $this->l('Price')
			  ),
			);
			
			$orderway = array(
			  array(
				'orderway' => 'ASC',                 // The value of the 'value' attribute of the <option> tag.
				'name' 	=> 	$this->l('Ascending')             // The value of the text content of the  <option> tag.
			  ),
			  array(
				'orderway' => 'DESC',                 // The value of the 'value' attribute of the <option> tag.
				'name' 	=> 	$this->l('Descending')             // The value of the text content of the  <option> tag.
			  ),
			);
			
			$pmanufacturers = $this->getManufacturers(Context::getContext()->shop->id);
			$selected_cat = array();
			if($data){
				if($data['params'] &&  isset($data['params']['categories']) && $data['params']['categories'])
					$selected_cat = $data['params']['categories'];
				if($data['params'] &&  isset($data['params']['pmanufacturer']) && $data['params']['pmanufacturer'])
					$data['params']['pmanufacturer[]'] = $data['params']['pmanufacturer'];
			}

			$this->fields_form[1]['form'] = array(
	            'legend' => array(
	                'title' => $this->l('Carousel Form.'),
	            ),
	            'input' => array(
	                array(
		                'type' => 'html',
		                'html_content' => 'Please access <a href="http://apollotheme.com/" target="_blank" title="apollo site">Apollotheme.com</a> to buy professional version to use this ',
		            ),
		            array(
		                'type' => 'html',
		                'html_content' => '<a target="_blank" href="http://apollotheme.com/how-to-buy-pro-version/" target="_blank" title="How to buy">How to buy Professional Version</a>',
		            ),
		            array(
		                'type' => 'html',
		                'html_content' => '<a target="_blank" href="http://apollotheme.com/different-between-free-pro-version/" target="_blank" title="Why should use">Why should use Professional Version</a>',
		            )
	            ),
	      		'buttons' => array(
                            array(
                                'title' => $this->l('Save And Stay'),
                                'icon' => 'process-icon-save',
                                'class' => 'pull-right',
                                'type' => 'submit',
                                'name' => 'saveandstayleotempcp'
                            ),
                            array(
                                'title' => $this->l('Save'),
                                'icon' => 'process-icon-save',
                                'class' => 'pull-right',
                                'type' => 'submit',
                                'name' => 'saveleotempcp'
                            ),
                        )
	        );

 
		 	$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			
			$helper->tpl_vars = array(
	                'fields_value' => $this->getConfigFieldsValues( $data  ),
	                'languages' => Context::getContext()->controller->getLanguages(),
	                'id_language' => $default_lang
        	); 
			return  $helper->generateForm( $this->fields_form );

		}

		public function renderContent( $args, $setting ){
			$t  = array(
				'name'=> '',
				'html'   => '',
			);
			$setting = array_merge( $t, $setting );
						
			$nb = ($setting['itemstab']) ? (int)($setting['itemstab']) : 6;
            $orderby = ($setting['orderby']) ? ($setting['orderby']) : 'date_add';
			$orderway = ($setting['orderway']) ? ($setting['orderway']) : 'ASC';
			$items_page 	= 	($setting['itemspage']) ? (int)($setting['itemspage']) : 3;
            $columns_page 	= ($setting['columns']) ? (int)($setting['columns']) : 3;
			$interval 		= 	(isset($setting['interval'])) ? (int)($setting['interval']) : 8000;
			switch ( $setting['source'] ) {
				case 'ptype':
						 switch ( $setting['ptype'] ) {
								case 'newest':
									 $products = Product::getNewProducts($this->langID, 0, $nb,false,$orderby,$orderway );
									break;
								case 'featured':
									$category = new Category(Context::getContext()->shop->getCategory(), $this->langID );
									$products = $category->getProducts((int)$this->langID, 1, $nb,$orderby,$orderway);
									break;
								case 'bestseller':
									$products = ProductSale::getBestSalesLight((int)$this->langID, 0, $nb);
									break;	
								case 'special': 
									 $products = Product::getPricesDrop( $this->langID, 0, $nb,false,$orderby,$orderway);
									break;
					}

				
				break;
					
				case 'pproductids':
						
						$where = '';
						if(empty($setting['pproductids']))
							return false;
						if($pproductids = $setting['pproductids'])
							$where = 'WHERE  p.id_product IN  (' . pSQL($pproductids) . ')';
							
						$products = $this->getProducts($where,(int) Context::getContext()->language->id, 1, $nb, $orderby,$orderway);
						
				 break;
				 
				 case 'pcategories':		
						$where = '';
						$catids = (isset($setting['categories']) && $setting['categories']) ? ($setting['categories']) : array();
						if($catids){
							$categorys = implode(",",$catids);
							$where = 'WHERE  cp.id_category IN  (' . pSQL($categorys) . ')';
						}
						$products = $this->getProducts($where,(int) Context::getContext()->language->id, 1, $nb, $orderby,$orderway);
						
				 break;
				 
				 case 'pmanufacturers':		
						$where = '';
						$manufacturers = ($setting['pmanufacturer']) ? ($setting['pmanufacturer']) : array();
						if($manufacturers){
							$manufacturers = implode(",",$manufacturers);
							$where = 'WHERE  p.id_manufacturer IN  (' . pSQL($manufacturers) . ')';
						}
						$products = $this->getProducts($where,(int)Context::getContext()->language->id, 1, $nb, $orderby,$orderway);
						
				 break;
			}
			Context::getContext()->controller->addColorsToProductList($products);
			$setting['products'] = $products;
			$setting['itemsperpage'] = $items_page; 
			$setting['columnspage'] = $columns_page; 
			$setting['scolumn']    = 12 / $columns_page;
			$setting['interval'] 	= $interval;
			$setting['homeSize'] = Image::getSize(ImageType::getFormatedName('home'));
			$setting['tab'] 	= 'leoproductcarousel'.rand(20,rand());
			$output = array('type'=>'carousel','data' => $setting );

			return $output;
		} 
		
	
	}
?>