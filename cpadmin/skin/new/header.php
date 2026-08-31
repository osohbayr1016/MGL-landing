<div class="row border-bottom">
        <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm  welcome-message">Хэлний сонголт</span>
                </li>
                <li class="dropdown">
                    <a class="count-info btn btn-warning"  data-toggle="dropdown" href="#">
                    <?php echo $selAdminLang["langKey"];?>
                    </a>
                    <ul class="dropdown-menu ">
                        <?php
                        foreach($sysLangArr as $key=>$obj){
                        ?>
                        <li>
                            <a href="/changeLang/<?php echo $obj["langID"];?>"><?php echo $obj["langName"];?> | <?php echo $obj["langKey"];?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                


                <li>
                    <a href="/modu/logout">
                        <i class="fa fa-sign-out"></i> Гарах
                    </a>
                </li>
                
            </ul>

        </nav>
        </div>