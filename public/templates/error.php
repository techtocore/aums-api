<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login with AUMS</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            /* Margin bottom by footer height */
            margin-bottom: 60px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
            padding-top: 17px;
            text-align: center;
            background-color: #fbfbfb;
        }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container" style="margin-top: 20px;">
    <div class="row">
        <div class="col-sm-12 hidden-xs" style="height: 100px;">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" style="text-align: center">
            <h5>Invalid OAuth 2.0 Authorization request</h5>
            <code>
                <?=$this->data['response']->error_description?>
            </code>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Designed & developed by <a href="https://github.com/niranjan94" target="_blank">@niranjan94</a>.</p>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>