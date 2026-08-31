<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-12">
                    <h2>Ажлын файл</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Эхлэл</a>
                        </li>
                        <li>
                            <a href="/users">Харилцагч бүртгэх</a>
                        </li>
                        <li class="active">
                            <strong><?php echo $selUserObj["companyName"]?></strong>
                        </li>
                    </ol>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>"<?php echo $selUserObj["companyName"]?>" харилцагчийн ажлын файлын жагсаалт</h5>
                            <div class="ibox-tools">
                                <a href="/users/fileadd/<?php echo $userID;?>" class="btn btn-primary btn-xs accessModBtn">Шинэ файл нэмэх</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="project-list">

                                <table class="table table-hover">
                                	<thead>
                                	<tr>
                                        <th>№</th>
                                        <th>Файлын нэр</th>
                                        <th>Файлын хэмжээ</th>
                                        <th>Файлын линк</th>
                                        <th>Огноо</th>
                                        <th> </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									if(count($fileArr)>0)
                                    foreach($fileArr as $key=>$obj){
									?>
                                    <tr>
                                    	<td class="project-status">
                                            <span class="label label-success"><?php echo ($key+1)?></span>
                                        </td>
                                        <td class="project-title">
                                            <?php echo $obj["fileName"];?>
                                        </td>    
                                        <td >
                                            <?php echo $obj["fileSize"];?>
                                        </td>                                    
                                        <td >
                                            <?php echo $obj["fileLink"];?>
                                        </td>
                                        <td class="project-completion">
											<small><?php echo $obj["createDate"];?></small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="<?php echo "/users/fileedit/".$obj["id"];?>" class="btn btn-warning btn-sm accessModBtn"><i class="fa fa-pencil"></i> засах </a>
											<a href="<?php echo "/users/filedelete/".$obj["id"];?>" class="btn btn-danger btn-sm accessModBtn"><i class="fa fa-close"></i> Устгах </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>

                                <a href="/users" class="btn btn-sm btn-primary">Буцах</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<div class="modal inmodal" id="orderModalFrm" tabindex="-1" role="dialog" aria-hidden="true">
    
</div>