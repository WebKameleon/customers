<?php
	$webtd=new webtdModel($this->webtd['sid']);
	$weblink=new weblinkModel();
	
	if (!$this->webtd['staticinclude'] || $this->webtd['ob']!=3)
	{
		
		$webtd->ob=3;
		$webtd->staticinclude=1;
		$webtd->save();
	}
	
	$tds=$webtd->getAll(array($this->webtd['page_id']));
	
	$tabs=array();
	foreach($tds AS $td)
        {
		if ($td['level']!=$webtd->level) continue;
		if ($td['sid']==$webtd->sid) continue;
		
		if (!$td['title'] && $td['menu_id']) {
			
			$links=$weblink->getAll($td['menu_id']);
			if (is_array($links) && isset($links[0]))
			{
				$td['title']=$links[0]['name'];
			}
		}
		$tabs[]=['sid'=>$td['sid'],'title'=>$td['title'],'first'=>count($tabs)==0];
        }
	

?>


<ul class="nav nav-tabs" id="myTab" role="tablist"><?php foreach ($tabs AS $tab): ?>
	<li class="<?php if ($tab['first']) echo 'active';?>" role="presentation"><a aria-controls="tab-<?php echo $tab['sid']?>" aria-expanded="true" data-toggle="tab" href="#tab-<?php echo $tab['sid']?>" id="tab-<?php echo $tab['sid']?>-tab" role="tab"><?php echo $tab['title']?></a></li>
<?php endforeach;?></ul>
