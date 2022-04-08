<html>
    <body>

        Hello!

        <p>
            Here is the ChatGive security code you need to continue:
            <br><br>
            <?= $code ?>
        </p>

        <div style="font-size: 90%; font-style: italic">
            This email was generated because of a login/registration attempt from a web or mobile device located at <?= get_client_ip_from_trusted_proxy() ?>
        </div>

        <br>

        <div>
            <?= FOOTER_TEXT ?>
        </div>
    </body>
</html>