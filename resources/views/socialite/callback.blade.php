<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback</title>
    <script>
        window.opener.postMessage({ token: "{{ $token }}", name: "{{ $user }}" }, "YOUR DOMAIN");
        // window.close();
      </script>
</head>
<body>
    Callback
</body>
</html>
