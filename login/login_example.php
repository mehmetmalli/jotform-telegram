<?php

$formId = $_GET["formId"];
$username = $_GET["username"];
$apiKey = $_GET["apiKey"];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>JotForm Telegram Login</title>
</head>

<body>
    <center>
        <div id="message"></div>
    </center>
    <center id="telegram-center">
        <script type="text/javascript">
            let apiKey = "<?php echo $_POST["apiKey"] ?>";

            function onTelegramAuth(user) {
                let api_key = "<?php echo $apiKey ?>";
                let form_id = "<?php echo $formId ?>"
                let username = "<?php echo $username ?>"
                console.log(user);
                fetch("https://telegram.jotform.io/login/integration_login_handler.php", {
                    method: 'POST',
                    mode: 'no-cors',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        "chat_id": user.id,
                        "api_key": api_key,
                        "form_id": form_id,
                        "username": username
                    })
                }).then(() => {
                    if (user && api_key !== "" && form_id !== "") {
                        document.getElementById("message").innerHTML = "<h4>You have successfully connected your Telegram account and a webhook<br>has been added to this form to get Telegram notifications.</h4><br/><h3>You may now close this pop up.</h3>";
                        document.getElementById("telegram-center").style.display = "none";
                    } else {
                        document.getElementById("message").innerHTML = "<h1>Houston we have a problem!</h1>"
                    }
                })
            }
        </script>
        <script async src="https://telegram.org/js/telegram-widget.js?6" data-telegram-login="jotformtelegrambot" data-size="large" data-onauth="onTelegramAuth(user)" data-request-access="write">
        </script>
    </center>
</body>

</html>