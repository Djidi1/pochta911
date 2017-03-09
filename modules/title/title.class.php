<?php

class titleModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}
    public function get_assoc_array($sql){
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items[] = $row;
        }
        return $items;
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
    public function getPrices() {
        $sql = 'SELECT id, km_from, km_to, km_cost FROM routes_price r';
        return $this->get_assoc_array($sql);
    }
    public function getAddPrices() {
        $sql = 'SELECT id, type, cost_route FROM routes_add_price r';
        return $this->get_assoc_array($sql);
    }
}
class titleProcess extends module_process {
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
		/* actionDefault - Действие по умолчанию. Должно браться из БД!!! */		$this->actionDefault = '';
		$this->actionsColl = new actionColl ();
		$this->nModel = new titleModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new titleView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Главная страница', ACTION_GROUP );
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}
	}
	public function update($_action = false) {
		$this->updated = false;
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

        $this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
        $this->updated = true;

		/********************************************************************************/
		if ($action == 'view') {
            $news = $this->nModel->getNewsList(3);
            $prices = $this->nModel->getPrices();
            $add_prices = $this->nModel->getAddPrices();
			$this->nView->view_Index ( $news, $prices, $add_prices );
			$this->updated = true;
		}
		
		/********************************************************************************/
		
	}
}
/*************************************/
class titleView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function view_Index($news, $prices, $add_prices) {
		$Container = $this->newContainer ( 'index' );
		$this->pXSL [] = RIVC_ROOT . 'layout/' . $this->modName . '/index.view.xsl';

        $ContainerNews = $this->addToNode ( $Container, 'news', '' );
        foreach ( $news as $item ) {
            $this->arrToXML ( $item, $ContainerNews, 'item' );
        }
        $ContainerPrices = $this->addToNode ( $Container, 'prices', '' );
        foreach ( $prices as $item ) {
            $this->arrToXML ( $item, $ContainerPrices, 'item' );
        }
        $ContainerAddPrices = $this->addToNode ( $Container, 'add_prices', '' );
        foreach ( $add_prices as $item ) {
            $this->arrToXML ( $item, $ContainerAddPrices, 'item' );
        }
	}

}
/*************************************/
