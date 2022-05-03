<?php
//ACTIVATION COMPTE OU REDIRECTION
if(isset($_GET["verifyAccount"]) && isset($_GET["clientId"]) && isset($_GET["token"])){
    $database = new DB(DBNAME, DBHOST, DBUSER, DBPWD);
    $result=$database->query("SELECT user_email FROM `ma_users` WHERE `user_verify`='". $_GET["token"]."'");
    if (mysqli_num_rows($result) == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if(password_verify ($row['user_email'], $_GET["clientId"])){
            $_SESSION["verify"] = "1";
            $result=$database->query("UPDATE `ma_users` SET `user_verify`='1' WHERE `user_verify`='". $_GET["token"]."' AND `user_email`='". $row['user_email']."'");
            ?>
            <div class="content text-center m-3">
                <h2>Félicitation! votre compte à été activé!</h2>
            </div>
            <?php
        }else{
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            header('Location: ' . $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }else{
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        header('Location: ' . $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }
    //PAGE DE VERIFICATION DE COMPTE
}else if(isset($_SESSION['verify'])){
    $database = new DB(DBNAME, DBHOST, DBUSER, DBPWD);
    $result=$database->query("SELECT user_verify FROM `ma_users` WHERE `user_email`='". $_SESSION['email']."'");
    if (mysqli_num_rows($result) == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $_SESSION["verify"] = $row['user_verify'];
    }if(isset($_SESSION['verify'])&&strlen($_SESSION['verify'])>1){
        ?>
        <div class="content text-center m-3">
            <svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="envelope-open-text" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-envelope-open-text fa-w-16 fa-7x" style="max-width: 100px;max-height: 100px;margin-bottom: 20px;color: #80b982;">
                <g class="fa-group">
                    <path fill="currentColor" d="M64,257.6,227.9,376a47.72,47.72,0,0,0,56.2,0L448,257.6V96a32,32,0,0,0-32-32H96A32,32,0,0,0,64,96ZM160,160a16,16,0,0,1,16-16H336a16,16,0,0,1,16,16v16a16,16,0,0,1-16,16H176a16,16,0,0,1-16-16Zm0,80a16,16,0,0,1,16-16H336a16,16,0,0,1,16,16v16a16,16,0,0,1-16,16H176a16,16,0,0,1-16-16Z" class="fa-secondary" style="opacity: 0.3;color: forestgreen;"></path>
                    <path fill="currentColor" d="M352,160a16,16,0,0,0-16-16H176a16,16,0,0,0-16,16v16a16,16,0,0,0,16,16H336a16,16,0,0,0,16-16Zm-16,64H176a16,16,0,0,0-16,16v16a16,16,0,0,0,16,16H336a16,16,0,0,0,16-16V240A16,16,0,0,0,336,224ZM329.4,41.4C312.6,29.2,279.2-.3,256,0c-23.2-.3-56.6,29.2-73.4,41.4L152,64H360ZM64,129c-23.9,17.7-42.7,31.6-45.6,34A48,48,0,0,0,0,200.7v10.7l64,46.2Zm429.6,34c-2.9-2.3-21.7-16.3-45.6-33.9V257.6l64-46.2V200.7A48,48,0,0,0,493.6,163ZM256,417.1a80,80,0,0,1-46.9-15.2L0,250.9V464a48,48,0,0,0,48,48H464a48,48,0,0,0,48-48V250.9l-209.1,151A80,80,0,0,1,256,417.1Z" class="fa-primary" style=""></path>
                </g></svg>
            <h2>Vérification de votre compte</h2><br>

            <?php if($_SERVER["SERVER_NAME"]!="localhost"){?>

                <p style="max-width:500px">Un mail de confirmation à été envoyé à l'adresse: <b><?= $_SESSION['email']?></b><br> afin d'activer votre compte.</p><br>
                <p>Si vous n'avez pas reçu de mail, cliquez sur ce bouton</p>
                <span id="resend" class="btn btn-success">Renvoyer</span><br>

            <?php }else{
                $link = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
                if (strpos($link, "/content")) $link = substr($link, 0, strpos($link, "/content"));
                $link='http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' .$link."?verifyAccount&clientId=".password_hash($_SESSION['email'],PASSWORD_DEFAULT)."&token=".$_SESSION['verify'];?>

                <div id='error-fmail'>
                    <p>La fonction d'envoi d'email n'est pas disponible en 'localhost'<br>Cliquer ici pour activer votre compte</p>
                    <a href="<?=$link?>" class="btn btn-outline-secondary">Activer le compte</a>
                </div>

            <?php }?>
        </div>
        <script>
            $(function(){
                $('span#resend').on('click',function(){
                    $.ajax({
                        url : '<?= SUBDOMAINE?>/content/assets/lib/verifyAccount.php',
                        type : 'POST',
                        data : 'url&client=<?= $_SESSION['email']?>&token=<?= $_SESSION['verify']?>',
                        success : function(code_html){
                            if(code_html.includes("div class")){
                                $("#on-error-mail").html(code_html);
                            }
                        }
                    });
                })

            })
        </script>
    <?php }else{//RETOUR
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        header('Location: ' . $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }}
//SEND MAIL
if(isset($_POST['client'])&&isset($_POST['token'])){
    if($_SERVER["SERVER_NAME"]!="localhost"){
        $link = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        if (strpos($link, "/content")) $link = substr($link, 0, strpos($link, "/content"));
        $link='http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' .$link."?verifyAccount&clientId=".password_hash($_POST['client'],PASSWORD_DEFAULT)."&token=".$_POST['token'];
        $recipient = $_POST['client'];
        $subject = "Validation du profil";
        $message = '<html><body><p>Afin de finaliser votre inscription merci de bien vouloir confirmer votre e-mail en suivant ce lien.</p><br><a href="'.$link.'" target="_blank">Confirmer</a></body></html>';
        echo $message;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: Démo Framework <masterprime974@gmail.com>';
        if (mail($recipient, $subject, $message, implode("\r\n", $headers)))
        {
            echo "success";
        }
        else
        {
            echo "error";
        }
    }
}
?>