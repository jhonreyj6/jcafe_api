<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback</title>
    <script>
        window.opener.postMessage({ access_token: "{{ $access_token }}", user: "{{ $user }}" }, "https://j6cafe.com/login");
        window.close();
      </script>
</head>
<body>
    Please Wait...
</body>
</html>
