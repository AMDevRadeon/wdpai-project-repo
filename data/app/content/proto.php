<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            width: 100vw;
            height: fit-content;

            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        div {
            padding: 25px;
            border: 2px dotted black;
        }

        div:hover {
            background-color: yellow;
        }
    </style>
</head>
<body>
    <div>
        Hello, World!
    </div>
    <?php
        phpinfo();
    ?>
</body>
</html>