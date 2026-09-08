<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Complete Your Admin Registration</title>
    </head>
    <body>
        <h2>Complete your admin registration</h2>

        <p>
            You have been invited to complete your admin registration.
        </p>

        <p>
            Please click the link below to verify your email address and complete your registration:
        </p>

        <p>
            <a href="{{ $registration_url }}">
                {{ $registration_url }}
            </a>
        </p>

        <p>
            This registration link will expire after 30 minutes.
        </p>

        <p>
            If you did not expect this registration invitation, you can safely ignore this email.
        </p>
    </body>
</html>