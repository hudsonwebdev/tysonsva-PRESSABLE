<?php
function drawSocial(){ ?>

    <div class="social">
        <a href="https://www.instagram.com/tysons_va/" target="_blank" uk-icon="icon: instagram"></a>
        <a href="https://www.facebook.com/TysonsCommunityAlliance" target="_blank"  uk-icon="icon: facebook"></a>
        <a href="https://x.com/tysons_va"  target="_blank" uk-icon="icon: x"></a>
        <a href="https://www.youtube.com/@tysonscommunityalliance"  target="_blank" uk-icon="icon: youtube"></a>
        <a href="https://www.linkedin.com/company/tysons-community-alliance" target="_blank"  uk-icon="icon: linkedin"></a>

    </div>

<?php }



function drawSocialShare($title="",$url=""){ ?>
    <?php if($title>""){ ?>
        <h4 class="share-title">Share This:</h4>
    <?php } ?>
    <div class="social">
   
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" uk-icon="icon: facebook"></a>
    
        <a href="https://x.com/intent/tweet?url=<?php echo $url; ?>" uk-icon="icon: x"></a>
     
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" uk-icon="icon: linkedin"></a>

        <a href="mailto:?subject=<?php echo $title; ?>&body=<?php echo $url; ?>" uk-icon="icon: mail"></a>

       
    </div>
    
<?php }