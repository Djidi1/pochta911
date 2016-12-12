<?php

class ordersModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}
	
	
	public function getOrdersList($from, $to) {
		$sql = 'SELECT o.id, o.comment, o.cost, r.`from`, r.`to`, r.lenght, r.cost_route, s.status, u.name, u.title, o.dk, o.id_user
                  FROM ' . TAB_PREF . 'orders o
                LEFT JOIN ' . TAB_PREF . 'orders_routes r ON r.id_order = o.id
                LEFT JOIN ' . TAB_PREF . 'orders_status s ON s.id = o.id_status
                LEFT JOIN ' . TAB_PREF . 'users u ON u.id = o.id_user
                  WHERE o.dk BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                ORDER BY o.id desc
                LIMIT 0,1000';
		$this->query ( $sql );
		echo "<!-- ".$this->sql." -->";
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
//			$row ['from'] = $this->dateToRuFormat( $row ['from'] );
//			$row ['to'] = $this->dateToRuFormat( $row ['to'] );
			$items [] = $row;
		}
		return $items;
	}
	
	function dateToRuFormat($date) {
		$date = $this->mydate_to_dmy( $date );
		setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
		$date = strftime("%a, %d.%m.%Y", strtotime($date));
		return iconv('windows-1251','Utf-8', $date);
	}
	
	public function orderGet($tur_id) {
		$sql = 'SELECT t.`id`, t.`name`,`date`,c.`name` city_name, l.`name` loc_name, l.`id` loc_id,
		g.`name` gid_name, b.`number` bus_number,cost,currency, t.fire,
		(select count(*) from tc_tur_list tl WHERE tl.id_tur = t.`id`) turists
		FROM ' . TAB_PREF . '`tc_tur` t
		LEFT JOIN ' . TAB_PREF . 'tc_citys c ON t.id_city = c.id
		LEFT JOIN ' . TAB_PREF . 'tc_locations l ON t.id_loc = l.id
		LEFT JOIN ' . TAB_PREF . 'tc_gids g ON t.id_gid = g.id
		LEFT JOIN ' . TAB_PREF . 'tc_bus b ON t.id_bus = b.id
		WHERE t.`id` = '.$tur_id.'';
		//	stop($sql);
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row ['date'] = $this->mydate_to_dmy( $row ['date'] );
			$items [] = $row;
		}
		return $items;
	}
	


	
	public function orderUpdate($params) {
		$sql = 'UPDATE ' . TAB_PREF . '`tc_tourists` SET
		`name_f` = \'%1$s\',`name_i` = \'%2$s\',`name_o` = \'%3$s\',
		`dob` = \'%4$s\',`passport` = \'%5$s\',`phone` = \'%6$s\',
		`def_mp` = \'%7$u\',`country` = \'%8$u\',`comment` = \'%9$s\'
		WHERE `id` = \'%10$u\'';
		if (! $this->query ( $sql, $params['username_f'],$params['username_i'],$params['username_o'],
				$this->dmy_to_mydate($params['dob']),$params['passport'],$params['phone'],
				$params['def_mp'],$params['country'],$params['comment'],$params['user_id'] ))
			return false;
		return true;
	}



function mydate_to_dmy($date) {
	return date ( 'd.m.Y', strtotime ( substr ( $date, 0, 20 ) ) );
}

function dmy_to_mydate($date) {
	return date ( 'Y-m-d', strtotime (  $date ) );
}

public function getNewsList($limCount) {
      $page = 1;
      $limStart = ($page - 1) * $limCount;      
      $sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d.%%m.%%Y\') as time, 
			    	(SELECT COUNT(*) FROM news) as news_count
			     FROM news n
				ORDER BY n.`time` DESC';
      if ($limCount > 0) $sql.= ' LIMIT '.$limStart.','.$limCount;
      $this->query($sql);
      $collect = Array();
      while($row = $this->fetchRowA()) {      	
      	$collect[]=$row;
      }      
      return $collect;
    }

function GetInTranslit($string) {
	$replace=array(
			"'"=>"",
			"`"=>"",
			"а"=>"a","А"=>"a",
			"б"=>"b","Б"=>"b",
			"в"=>"v","В"=>"v",
			"г"=>"g","Г"=>"g",
			"д"=>"d","Д"=>"d",
			"е"=>"e","Е"=>"e",
			"ж"=>"zh","Ж"=>"zh",
			"з"=>"z","З"=>"z",
			"и"=>"i","И"=>"i",
			"й"=>"y","Й"=>"y",
			"к"=>"k","К"=>"k",
			"л"=>"l","Л"=>"l",
			"м"=>"m","М"=>"m",
			"н"=>"n","Н"=>"n",
			"о"=>"o","О"=>"o",
			"п"=>"p","П"=>"p",
			"р"=>"r","Р"=>"r",
			"с"=>"s","С"=>"s",
			"т"=>"t","Т"=>"t",
			"у"=>"u","У"=>"u",
			"ф"=>"f","Ф"=>"f",
			"х"=>"kh","Х"=>"kh",
			"ц"=>"tc","Ц"=>"tc",
			"ч"=>"ch","Ч"=>"ch",
			"ш"=>"sh","Ш"=>"sh",
			"щ"=>"shch","Щ"=>"shch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"e",
			"ю"=>"iu","Ю"=>"iu",
			"я"=>"ia","Я"=>"ia",
			"і"=>"i","І"=>"i",
			"ї"=>"yi","Ї"=>"yi",
			"є"=>"e","Є"=>"e"
	);
	return $str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
}

}

class ordersProcess extends module_process {
	public function __construct($modName) {
		global $values, $User, $LOG, $System;
		parent::__construct ( $modName );
		$this->Vals = $values;
		$this->System = $System;
		if (! $modName)
			unset ( $this );
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/*
		 * actionDefault - Действие по умолчанию. Должно браться из БД!!!
		 */
		$this->actionDefault = '';
		$this->actionsColl = new actionColl ();
		$this->nModel = new ordersModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new ordersView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Главная страница', ACTION_GROUP );
		$this->regAction ( 'order', 'Заявка', ACTION_GROUP );
		$this->regAction ( 'orderUpdate', 'Редактирование заявки', ACTION_GROUP );
		
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}
	}
	
	
	public function update($_action = false) {
		if ($_action)
			$this->action = $_action;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
		$user_id = $this->User->getUserID ();

		if ($user_id > 0) {
			$this->User->nView->viewLoginParams ( 'Доставка', '', $user_id, array (), array (), $this->User->getRight ( 'admin', 'view' ) );
		}
		

		
		if ($action == 'view') {
            $from = $this->Vals->getVal ( 'order', 'POST', 'string' );
            $to = $this->Vals->getVal ( 'order', 'POST', 'string' );
            if ($from == '') {
                $from = (isset($_SESSION['from']) and $_SESSION['from'] != '') ? $_SESSION['from'] : date('01.m.Y');
                $to = (isset($_SESSION['to']) and $_SESSION['to'] != '') ? $_SESSION['to'] : date('d.m.Y');
            }else{
                $_SESSION['from'] = $from;
                $_SESSION['to'] = $to;
            }
            $orders = $this->nModel->getOrdersList($from, $to);
			$this->nView->viewOrders ($from, $to, $orders);
		}
		

		
		if ($action == 'order') {
			$tur_id = $this->Vals->getVal ( 'order', 'GET', 'integer' );
			$tour = array();
			if ($tur_id > 0){
				$tour = $this->nModel->tourGet ( $tur_id );
			}
			$countris = $this->nModel->getCountrys ();
			$mp = $this->nModel->getMP ($tour[0]['loc_id']);
			$this->nView->viewOrderEdit ( $tour, $countris, $mp );
			
		}
		
		if ($action == 'orderUpdate') {
			$params['tur_id'] = $this->Vals->getVal ( 'tur_id', 'POST', 'integer' );
			$params['def_mp'] = $this->Vals->getVal ( 'def_mp', 'POST', 'integer' );
			$params['phone'] = $this->Vals->getVal ( 'phone', 'POST', 'string' );
			$params['name'] = $this->Vals->getVal ( 'name', 'POST', 'string' );
			$params['comment'] = $this->Vals->getVal ( 'comment', 'POST', 'string' );

			$id_tur_code = $this->nModel->addTuristInTour($params, $user_id);
			$this->nView->viewMessage('Заказ успешно добавлен. Номер для отслеживания: '.$id_tur_code, 'Сообщение');
		}

/** Поиск своего заказа */		
		if ($action == 'search_order') {
			$order = $this->Vals->getVal ( 'order_number', 'POST', 'integer' );
//			$items = $this->nModel->SearchOrder ( $order);
			$this->nView->viewSearchOrder ( /*$items*/ );
			
		}


		
		if ($this->Vals->isVal ( 'ajax', 'INDEX' )) {
			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.xls.xsl' );
				$PageAjax->addToPageAttr ( 'xls', '1' );
			} else
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.ajax.xsl' );
			$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
			$PageAjax->addToPageAttr ( 'isAjax', $isAjax );
			$html = $PageAjax->getBodyAjax2 ( $this->nView );
		
			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$reald = date ( "d.m.Y" );
				header ( "Content-Type: application/vnd.ms-excel", true );
				header ( "Content-Disposition: attachment; filename=\"list_" . $reald . ".xls\"" );
				exit ( $html );
			} else
				sendData ( $html );
		
		}


	}
	// Функции оплаты
	public function getSignature( $Shop_IDP, $Order_IDP, $Subtotal_P, $MeanType, $EMoneyType,
			$Lifetime, $Customer_IDP, $Card_IDP, $IData, $PT_Code, $password ) {
		$Signature = strtoupper(
				md5(
						md5($Shop_IDP) . "&" .
						md5($Order_IDP) . "&" .
						md5($Subtotal_P) . "&" .
						md5($MeanType) . "&" .
						md5($EMoneyType) . "&" .
						md5($Lifetime) . "&" .
						md5($Customer_IDP) . "&" .
						md5($Card_IDP) . "&" .
						md5($IData) . "&" .
						md5($PT_Code) . "&" .
						md5($password)
				)
		);
		return $Signature;
	}
	
	
	
}

class ordersView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function viewOrders($from, $to, $orders) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.view.xsl';
		$Container = $this->newContainer ( 'list' );

        $this->addAttr('from', $from, $Container);
        $this->addAttr('to', $to, $Container);

        $ContainerNews = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerNews, 'item' );
		}
		return true;
	}
	public function viewlocations($type,$items,$locs) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.locations.xsl';
		$Container = $this->newContainer ( 'locations' );
		$ContainerMenu = $this->addToNode ( $Container, 'menu', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $ContainerMenu, 'item' );
		}
		$ContainerType = $this->addToNode ( $Container, 'type', '' );
		foreach ( $type as $item ) {
			$this->arrToXML ( $item, $ContainerType, 'item' );
		}
		$ContainerLocs = $this->addToNode ( $Container, 'locs', '' );
		foreach ( $locs as $item ) {
			$this->arrToXML ( $item, $ContainerLocs, 'item' );
		}
		return true;
	}
	public function viewTurList($turs, $isAjax, $locs, $countris) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.turlist.xsl';
		if ($isAjax == 1) {
			$this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
		}
		$Container = $this->newContainer ( 'turlist' );
		$Containerusers = $this->addToNode ( $Container, 'turs', '' );
		foreach ( $turs as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		$ContainerLocs = $this->addToNode ( $Container, 'locs', '' );
		foreach ( $locs as $item ) {
			$this->arrToXML ( $item, $ContainerLocs, 'item' );
		}
		$ContainerCntr = $this->addToNode ( $Container, 'countris', '' );
		foreach ( $countris as $item ) {
			$this->arrToXML ( $item, $ContainerCntr, 'item' );
		}
		return true;
	}
	
	public function viewOrderEdit($tour, $countris, $mp) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.order.xsl';
		$Container = $this->newContainer ( 'orderedit' );
		$ContainerCountris = $this->addToNode ( $Container, 'tour', '' );
		foreach ( $tour as $item ) {
			$this->arrToXML ( $item, $ContainerCountris, 'item' );
		}
		$ContainerCountris = $this->addToNode ( $Container, 'countris', '' );
		foreach ( $countris as $item ) {
			$this->arrToXML ( $item, $ContainerCountris, 'item' );
		}
		$ContainerMP = $this->addToNode ( $Container, 'mp', '' );
		foreach ( $mp as $item ) {
			$this->arrToXML ( $item, $ContainerMP, 'item' );
		}
		return true;
	}
	
	public function viewSearchOrder ( $items ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_search.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		return true;
	}
	
	public function viewPayOrder ( $items,$frmData ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_pay.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		$this->arrToXML ( $frmData, $Containerusers, 'form_data' );

		return true;
	}
	

}