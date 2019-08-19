<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="//js.jotform.com/JotForm.min.js"></script>
    <title>JotForm Login</title>

</head>

<?php
$access_code = "";
$chat_id = null;
if (isset($_GET['access_code']) && !empty($_GET['access_code']) &&  isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $access_code = $_GET['access_code'];
    $chat_id = $_GET['chat_id'];
}
?>

<body>
    <h1 id="message">You may now close this window.</h1>
    <script>
        let url = 'https://telegram.jotform.io/login-on-tg/jot-login.php';
        let msg = document.getElementById("message");
        msg.style.display = "none";

        let access_code = "<?php echo $access_code; ?>";
        let chat_id = "<?php echo $chat_id; ?>";
        let api_key = null;

        if (access_code && chat_id !== 0) {
            JF.initialize({
                enableCookieAuth: true,
                appName: "JotForm Telegram Bot",
                accessType: 'full'
            });
            JF.login(
                () => {
                    api_key = JF.getAPIKey();
                    JF.getUser((user) => {
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                "api_key": api_key,
                                "access_code": access_code,
                                "chat_id": chat_id,
                                "username": user.username
                            })
                        }).then(() => {
                            msg.style.display = "block";
                        })
                    })
                }
            );
        }
    </script>
</body>

</html>