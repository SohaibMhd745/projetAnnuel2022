<?php
    $account_pages = array(
        "/account.php",
        "/signin.php",
        "/signup.php"
    );
?>

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
                    <a class="header-a <?php if($script_name=="/home.php") { ?>header-active<?php } ?>" href="/"><li class="header-li">Accueil</li></a>
                    <a class="header-a <?php if($script_name=="/shop.php") { ?>header-active<?php } ?>" href="/shop.php"><li class="header-li">Boutique</li></a>
                    <a class="header-a <?php if(in_array($script_name, $account_pages)) { ?>header-active<?php } ?>" href="/account.php"><li class="header-li">Espace compte</li></a>
                </ul>
            </div>
        </div>
    </div>
</div>