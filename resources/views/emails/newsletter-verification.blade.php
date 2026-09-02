
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify your newsletter subscription</title>
</head>
<body>

    <h2>Verify your email</h2>

    <p>
        You requested to subscribe to our newsletter.
    </p>

    <p>
        Please click the link below to verify your email address:
    </p>

    <p>
        <a href="{{ $verification_url }}">
            Verify my email
        </a>
    </p>

    <p>
        If you did not request this subscription, you can safely ignore this email.
    </p>

</body>
</html>
