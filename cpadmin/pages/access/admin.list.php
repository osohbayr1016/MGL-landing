<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Админ бүртгэл</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Эхлэл</a>
                        </li>
                        <li>
                            <a href="/access">Хандах эрх</a>
                        </li>
                        <li class="active">
                            <strong>Админ жагсаалт</strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Сайтын админы жагсаалт</h5>
                            <div class="ibox-tools">
                                <a href="/access/adminAdd" class="btn btn-primary btn-xs accessModBtn">Шинэ админ нэмэх</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            

                            <div class="project-list">

                                <table class="table table-hover">
                                	<thead>
                                	<tr>
                                        <th>Нэр</th>
                                        <th>Эрхийн бүлэг</th>
                                        <th> </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									if(count($adminArr)>0)
                                    foreach($adminArr as $key=>$obj){
										
									?>
                                    <tr>
                                        <td class="project-title">
                                            <?php echo $obj["name"];?>
                                        </td>
                                        <td class="project-title">
                                            <?php echo $obj["adminGroupName"];?>
                                        </td>	
                                        <td class="project-actions">                                        
                                            <a href="<?php echo "/access/adminEdit/".$obj["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
                                            <a href="<?php echo "/access/adminDel/".$obj["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-trash"></i> устгах </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<div class="modal inmodal" id="accessModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>