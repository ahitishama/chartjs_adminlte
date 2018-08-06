<?php
$fileName = $_GET['file'];
$sheet_number = $_GET['st'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin | Bescom</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../../../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../../../bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../../../dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

   <?php include '../../../header.php';?>
  <?php include '../../../nav.php';?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        GKS
        <small>Bangalore Electricity Supply Company Limited</small>
        <h6>Progress & pending Application details of Gangakalyana as on Jan-18</h6>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Charts</a></li>
        <li class="active">gks</li>
      </ol>
    </section>

    <!-- Main content -->
        <section class="content">
      <div id="dynamicChartPrint">
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include '../../../footer.php';?>

  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="../../../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="../../../bower_components/chart.js/Chart.js"></script>
<!-- FastClick -->
<script src="../../../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../../dist/js/demo.js"></script>
<!-- page script -->
<script>
 $(function () {
   	var fileName = "<?=$fileName;?>";
	var sheetNumber = "<?=$sheet_number;?>";
	
    var htmlContent = '';
    var allChartData='';
    var dataMapAll = [];

var ajaxURL = '';
	
	if(fileName == "mc_ea" && sheetNumber=="sheet1"){
		ajaxURL = "m_c/M_C-sheet1.php?method=Sheet1";
		
	}else if(fileName == "mc_ea" && sheetNumber=="sheet2"){
		ajaxURL = "m_c/M_C-sheet2.php?method=Sheet2";

	}else if(fileName == "mc_ea" && sheetNumber=="sheet4"){
		ajaxURL = "m_c/M_C-sheet4.php?method=Sheet4";
	}
	else if(fileName == "gks" && sheetNumber=="sheet1"){
		ajaxURL = "../../../gks/gks-sheet1.php?method=Sheet1";
	}
	
	if(ajaxURL==""){
		alert("URL not found");
		return false;
	}
    $.get("bmazoperation.html", function(html_string)
     {
        htmlContent = html_string;

        $.ajax({
            type: "GET",
            url: ajaxURL,
             success: function(data) {
              allChartData = $.trim(data);
            },
            async: false,
        });

        var allObject = $.parseJSON( allChartData );

        var dynm_lbl = allObject['0'];
        var chartObject = allObject['1'];
        var selectHtmlData = allObject['2'];
        dynm_lbl = jQuery.grep(dynm_lbl, function(n, i){
          return (n !== "" && n != null);
        });


        var htmlIndex = 1;

        $.each(selectHtmlData, function (key, data) {
          var partData = html_string;
          var printData = partData.replace(/{{circle}}/g, key);
          printData = printData.replace(/{{selectData}}/g, data);
          printData = printData.replace(/{{chartindex}}/g, htmlIndex);
	
          $('#dynamicChartPrint').append(printData);

          var dataMap = {};

          var circleObj = chartObject[key];

          var row = 1;

          $.each(circleObj, function (circleKey, circledata) {
  
              var obj = {};
              obj["method"] = 'Bar';
              var mainData = {};
              mainData['labels'] = dynm_lbl;
              mainData['datasets'] = circledata;
              obj["data"] = mainData;
            
              var newIndex = key+''+row;
              dataMap[newIndex] = obj;
              row++;

          });

          dataMapAll[key] = dataMap;

          console.log(dataMap);

         updateChart(dataMap,htmlIndex);
         htmlIndex++;

      })

        var currentChart;
        var params = '';

        function updateChart(dataMap1 = null,htmlIndex=null) {
            var currentCirlce = $(this).attr("data-circle");

            if(currentCirlce != undefined){
              dataMap1 = dataMapAll[$(this).attr("data-circle")];
              htmlIndex = $(this).attr("data-size");
              //alert(currentCirlce);
            }

            ctx = document.getElementById("barChart"+htmlIndex).getContext("2d");
             //if(currentChart){currentChart.destroy();}
            var determineChart = $("#chartType"+htmlIndex).val();
            params = dataMap1[determineChart];
            
            currentChart = new Chart(ctx)[params.method](params.data, {});
			
            var legend=currentChart;
            document.getElementById("js-legend"+htmlIndex).innerHTML = legend.generateLegend();
			
         } 

        $('.chartTypeClick').on('click', updateChart);

     },'html');   

  })
  
  

</script>



</body>
</html>
<style type="text/css">
 .bar-legend li
 {
    display:none !important; 
 }
  #js-legend ul {
  list-style-type: none;
    margin: 0;
    padding: 0;
}
.chart-legend ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
#js-legend ul li, .chart-legend ul li {
display: inline;
    padding-left: 30px;
    position: relative;
    margin-bottom: 4px;
    border-radius: 5px;
    padding: 2px 3px 2px 15px;
    font-size: 14px;
    cursor: default;
  -webkit-transition: background-color 200ms ease-in-out;
  -moz-transition: background-color 200ms ease-in-out;
  -o-transition: background-color 200ms ease-in-out;
  transition: background-color 200ms ease-in-out;
}

#js-legend li span, .chart-legend li span {
display: inline;
  position: absolute;
  left: 0;
  top: 0;
  width: 12px;
  height: 100%;
  border-radius: 5px;
}
/* legend2 */
  #js-legend2 ul {
  list-style-type: none;
    margin: 0;
    padding: 0;
}

#js-legend2 ul li {
display: inline;
    padding-left: 30px;
    position: relative;
    margin-bottom: 4px;
    border-radius: 5px;
    padding: 2px 3px 2px 15px;
    font-size: 10px;
    cursor: default;
  -webkit-transition: background-color 200ms ease-in-out;
  -moz-transition: background-color 200ms ease-in-out;
  -o-transition: background-color 200ms ease-in-out;
  transition: background-color 200ms ease-in-out;
}

#js-legend2 li span {
display: inline;
  position: absolute;
  left: 0;
  top: 0;
  width: 12px;
  height: 100%;
  border-radius: 5px;
}
/* legend3 */
  #js-legend3 ul {
  list-style-type: none;
    margin: 0;
    padding: 0;
}

#js-legend3 ul li {
display: inline;
    padding-left: 30px;
    position: relative;
    margin-bottom: 4px;
    border-radius: 5px;
    padding: 2px 3px 2px 15px;
    font-size: 14px;
    cursor: default;
  -webkit-transition: background-color 200ms ease-in-out;
  -moz-transition: background-color 200ms ease-in-out;
  -o-transition: background-color 200ms ease-in-out;
  transition: background-color 200ms ease-in-out;
}

#js-legend3 li span {
display: inline;
  position: absolute;
  left: 0;
  top: 0;
  width: 12px;
  height: 100%;
  border-radius: 5px;
}
/* legend4 */
  #js-legend4 ul {
  list-style-type: none;
    margin: 0;
    padding: 0;
}

#js-legend4 ul li {
display: inline;
    padding-left: 30px;
    position: relative;
    margin-bottom: 4px;
    border-radius: 5px;
    padding: 2px 3px 2px 15px;
    font-size: 10px;
    cursor: default;
  -webkit-transition: background-color 200ms ease-in-out;
  -moz-transition: background-color 200ms ease-in-out;
  -o-transition: background-color 200ms ease-in-out;
  transition: background-color 200ms ease-in-out;
}

#js-legend4 li span {
display: inline;
  position: absolute;
  left: 0;
  top: 0;
  width: 12px;
  height: 100%;
  border-radius: 5px;
}

</style>
