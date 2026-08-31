<?php 
if(count($subSchList)>0)
foreach($subSchList as $skey=>$sobj){		
?>
<div id="courseSch<?php echo $sobj["schID"];?>">
	<strong><?php echo $sobj["schOrder"].". ".$sobj["schTitle"];?></strong> 
    <a href="/insert/editSSch/<?php echo $sobj["schID"];?>" class="btn btn-primary btn-xs accessModBtn">Засах</a>
    <a href="/insert/delSSch/<?php echo $sobj["schID"];?>" class="btn btn-danger btn-xs accessModBtn">Устгах</a>
</div>
<?php
}
?>