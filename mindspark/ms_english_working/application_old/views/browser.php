<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mindspark English</title>
    <base href= <?php echo base_url(); ?> ></base>
    <link type="text/css" href="theme/css/bootstrap.min.css" rel="stylesheet" />
   <style type="text/css">
   #orange {
        background: #2f99cb none repeat scroll 0 0;
        float: left;
        height: 5px;
        width: 25%;
    }
    #yellow {
        background: #fbd212 none repeat scroll 0 0;
        float: left;
        height: 5px;
        width: 25%;
    }
    #blue {
        background: #e75903 none repeat scroll 0 0;
        float: left;
        height: 5px;
        width: 25%;
    }
    #green {
        background: #9ec956 none repeat scroll 0 0;
        float: left;
        height: 5px;
        width: 25%;
    }
    .table-browser{
        margin-left: 20%;
        margin-top: 40px;
        width: 50%;
    }
    .message{
        color:red;
        /*background:red;*/
        width:70%;
        margin-left:12%;
        margin-top: 20px;
        text-align: center;
    }
   </style>
</head>
<body>
    <div class="row" style="margin-right: inherit!important; margin-left: inherit!important;">
        <div id="header">
            <div id="eiColors">
                <div id="orange"></div>
                <div id="yellow"></div>
                <div id="blue"></div>
                <div id="green"></div>
            </div>
        </div>
        <div class="message"><strong>YOUR BROWSER IS NOT SUPPORTED. PLEASE UPGRADE YOUR BROWSER TO CONTINUE USING MINDSPARK.</strong></div>
        <div id="head" class="table-browser"> 
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Browser/Device</th>
                    <th>Version</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Mozilla Firefox</td>
                    <td>35 and above</td>
                  </tr>
                  <tr>
                    <td>Google Chrome</td>
                    <td>38 and above</td>
                  </tr>
                  <tr>
                    <td>Internet Explorer</td>
                    <td>10 and above</td>
                  </tr>
                  <tr>
                    <td>Safari</td>
                    <td>7 and above</td>
                  </tr>
                  <tr>
                    <td>Android</td>
                    <td>4 and above</td>
                  </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
