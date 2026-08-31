<?php 
if(count($subSchList)>0)
foreach($subSchList as $skey=>$sobj){	
    $selSchBody = json_decode($sobj["schNote"],true);	
?>
<div id="courseSch<?php echo $sobj["schID"];?>">
	<strong><?php echo $sobj["schOrder"].". ".$selSchBody["title"];?></strong> 
    <a href="/insert/pageSubSchEdit/<?php echo $sobj["schID"];?>" class="btn btn-primary btn-xs accessModBtn">Засах</a>
    <a href="/insert/pageSubSchDel/<?php echo $sobj["schID"];?>" class="btn btn-danger btn-xs accessModBtn">Устгах</a>
</div>
<?php
}
?>