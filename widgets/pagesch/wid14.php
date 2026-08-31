<div class="wrapper">
    <div class="featured-strip-wrap">
        <div class="featured-strip-inner">
            <div class="featured-strip-content">
                <h2><?php echo $selSchBody["title"];?></h2>
                <h5><?php echo $selSchBody["note"];?></h5>
                <a href="<?php echo $selSchBody["link"];?>" class="button button-white"></a>
            </div>
            <div class="featured-strip-image">
                <?php
                function get_youtube_id_from_url($url)  {
                    preg_match('/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $results);    return $results[6];
               }

                $vidId = get_youtube_id_from_url($selSchBody["vlink"]);
                if($vidId != ""){
                   

                ?>
                <div style="height:100%;width:100%;"><iframe  style="height:100%;width:100%;" src="https://www.youtube.com/embed/<?php echo $vidId;?>?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

                <?php }
                else{?>
                <video class="lozad" autoplay muted loop playsinline>
                    <source src="<?php echo $objs["schPic"];?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>
