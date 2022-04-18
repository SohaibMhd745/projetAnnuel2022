<div class="container-fluid header-fluid">
    <div class="container">
        <div class="row header-row">
            <div class="col header-col header-col1">
                <a href="/">
                    <img src="/assets/logo/logo_right_white.png" width="144px">
                </a>
            </div>
            <div class="col header-col header-col2">
                <ul class="header-ul">
                    <a class="header-a <?php if($page_name=="home") { ?>header-active<?php } ?>" href="/"><li class="header-li">Accueil</li></a>
                    <a class="header-a <?php if($page_name=="shop") { ?>header-active<?php } ?>" href="/shop"><li class="header-li">Boutique</li></a>
                    <a class="header-a <?php if($page_name=="cart") { ?>header-active<?php } ?>" href="/cart"><li class="header-li">Panier</li></a>
                    <a class="header-a <?php if($page_name=="account") { ?>header-active<?php } ?>" href="/account"><li class="header-li">Espace compte</li></a>
                </ul>
            </div>
        </div>
    </div>
</div>