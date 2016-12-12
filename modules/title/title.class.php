<?php

class titleModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}
	
	public function getIndex() {

		
		/***** Зависимость от прав группы на использование модулей ***/
		$user_id=$this->User->getUserID();

		$sql='SELECT group_id
  				FROM '.TAB_PREF.'groups_user
  				WHERE user_id='.$user_id;
		$this->query ($sql);
		$group = $this->fetchOneRowA ();
		$group = $group['group_id'];
		$sql = "SELECT sm.parent
		  ,sm.name as name
		  ,sm.id
		  ,sm.url
		  ,smp.desc as desc
		  ,smp.name_en as modul
      
  FROM ".TAB_PREF."menu sm (nolock)
  left join ".TAB_PREF."menu smp (nolock) on sm.parent=smp.id
  left join ".TAB_PREF."modules m (nolock) on sm.url like '/'+m.codename+'/'
  left join ".TAB_PREF."module_actions mact (nolock) on mact.mod_id=m.id and (mact.action_name=m.defAction)
  left join ".TAB_PREF."module_actions mact2 (nolock) on sm.url like '%%/'+mact2.action_name+'-%%'
  left join ".TAB_PREF."modules m2 (nolock) on m2.id=mact2.mod_id and sm.url like '/'+m2.codename+'/%%'
  left join ".TAB_PREF."module_access mac (nolock) on mac.action_id=mact.id and mac.group_id=".$group."  -- номер группы
  left join ".TAB_PREF."module_access mac2 (nolock) on mac2.action_id=mact2.id and mac2.group_id=".$group."
  where 
  smp.isen > 0 and sm.isen > 0 and 
  (mact.action_name is not null or m2.id is not null)
  and (mac.access=1 or mac2.access=1)
  order by sm.parent";

		$this->query ($sql);
		$titleCollection = array ();
		$test='';
		while ( ($row = $this->fetchRowA ()) !== false ) {
			if ($test!=$row['modul']){$test=$row['modul'];}
			$titleCollection[$test][] = $row;
		}
		return $titleCollection;
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

		$user_right = $this->User->getRight ( $this->modName, $action );
		if ($user_right == 0 && $user_id > 0) {
			$p = array ('У Вас нет прав для использования модуля', '$this->modName' => $this->modName, 'action' => $action, 'user_id' => $user_right, 'user_right' => $user_right );
			$this->nView->viewError ( 'У Вас нет прав на это действие.', 'Предупреждение' );
			$this->Log->addError ( $p, __LINE__, __METHOD__ );
			$this->updated = true;
			return;
		}
		if ($user_right == 0 && ! $_action) {
			$this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
			$this->updated = true;
			return;
		}
		
		
		/********************************************************************************/
		if ($action == 'view') {
			$item = $this->nModel->getIndex ();
            $news = $this->nModel->getNewsList(3);
			$this->nView->view_Index ( $item, $news );
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
	
	public function view_Index($items, $news) {
		$Container = $this->newContainer ( 'index' );
		$itemConteiner = $this->addToNode ( $Container, 'items', '' );
		$this->pXSL [] = RIVC_ROOT . 'layout/' . $this->modName . '/index.view.xsl';
		foreach ( $items as $key => $item ) {
			$itemConteiner2 = $this->addToNode ( $itemConteiner, 'item', '' );
			$this->addAttr ( 'modul', $key, $itemConteiner2 );
			foreach ( $item as $mod ) {
				try {
					$this->arrToXML ( $mod, $itemConteiner2, 'mod', array () );
				} catch ( Exception $e ) {
					stop ( 'Caught exception: ' . $e->getMessage (), "\n" );
				}
			}
		}
        $ContainerNews = $this->addToNode ( $Container, 'news', '' );
        foreach ( $news as $item ) {
            $this->arrToXML ( $item, $ContainerNews, 'item' );
        }
	}

}
/*************************************/
