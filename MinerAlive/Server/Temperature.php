

<div id="dom-target" style="display: none;">
    <?php 
       include "GetData.php";
       $SensorData = GetTempData();
       $ExtremeTempDataValues = GetExtremeTempData();

    ?>
</div>







<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="Bootstrap4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Company name</a>
      <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="#">Sign out</a>
        </li>
      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link active" href="index.php">
                  <span data-feather="home"></span>
                  Dashboard 
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="Temperature.php">
                  <span data-feather="file"></span>
                  Temperaturen <span class="sr-only">(current)</span>
                </a>
              </li>
               <li class="nav-item">
                <a class="nav-link" href="MinerAlive.php">
                  <span data-feather=""></span>
                  MinerAlive
                </a>
              </li> 
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Saved reports</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Current month
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Last quarter
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Social engagement
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Year-end sale
                </a>
              </li>
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
          
            
              <div id="chartContainer" style="height: 600px; width: 100%;"></div>
            <?php //GetTempData(); ?>
          
          <p id="demo"></p>


          <h2>Temperature</h2>
          <div class="table-responsive">
            <table class="table table-striped table-sm">
             <thead>
                <tr>
                  <th><?php echo implode('</th><th>', array_keys($sqlGpu[0])); ?></th>
                </tr>
              </thead>
               <tbody>
<?php





foreach ($sqlGpu as $row)
{

  if(json_decode($row["Temperature"]))
  {
    $TemperatureOutput = NULL;
    $tempData = json_decode($row["Temperature"], JSON_PRETTY_PRINT);
    $TemperatureOutput .= "<table>";
      $TemperatureOutput .= "<thead>";
        $TemperatureOutput .= "<tr>";
          $TemperatureOutput .= "<th>";
            $TemperatureOutput .= implode('</th><th>', array_keys($tempData));
          $TemperatureOutput .= "</th>";
        $TemperatureOutput .= "</tr>";
      $TemperatureOutput .= "</thead>";
      $TemperatureOutput .= "<tbody>";
      $TemperatureOutput .= "<tr>";
    foreach ($tempData as $row2)
    {
          //$TemperatureOutput .= array_map('htmlentities', $row);
          $TemperatureOutput .= "<td>";
          foreach ($row2 as $key => $value) {
            $TemperatureOutput .= "";
            $TemperatureOutput .= $key . " ";
            $TemperatureOutput .= $value;
            $TemperatureOutput .= "<br/>";
          }
          $TemperatureOutput .= "</td>";     

    }
       $TemperatureOutput .= "</tr>";
       $TemperatureOutput .="</tbody>";
      $TemperatureOutput .="</table>";
    $row["Temperature"] = $TemperatureOutput;
  }


  array_map('htmlentities', $row);

    echo "<td>";
    echo implode('</td><td>', $row);
    echo "</td>";
      echo "</tr>";
}
?>
  </tbody>
            </table>
          </div>
        </main>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="Bootstrap4.1.1/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="Bootstrap4.1.1/assets/js/vendor/popper.min.js"></script>
    <script src="Bootstrap4.1.1/dist/js/bootstrap.min.js"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace()
    </script>




  <script type="text/javascript">
  
    var updateChart = function () {

    var SensorData = <?php echo json_encode($SensorData); ?>;
    var ExtremeTempDataValues = <?php echo json_encode($ExtremeTempDataValues); ?>;
    var ymax =    parseInt(ExtremeTempDataValues["Max"])+5;                 
    var ymin =    parseInt(ExtremeTempDataValues["Min"])-5;                 

    //console.log(SensorData);
    console.log(ExtremeTempDataValues);

    var dps = [];
    var Series = [];
    var chart = new CanvasJS.Chart("chartContainer",{
      zoomEnabled: true,
      zoomType: "xy",

  title :{
    text: "Live Data"
  },
  axisX: {            
    title: "Axis X Title"
  },
  axisY: {            
    title: "Units",
       maximum: ymax,
   minimum: ymin,
  },
  data: Series
});
    
console.log(chart);


      Object.keys(SensorData).forEach(function(sensorId) {
        dps[sensorId] = [];
        Series.push({
            type:"line",
            xValueType: "dateTime",
            dataPoints : dps[sensorId],
            name : sensorId,
            toolTipContent: "x:{x}, y: {y} <br/> name: {name}"
          })
          
          Object.keys(SensorData[sensorId]).forEach(function(index) {
            
           dps[sensorId].push({x: parseInt(SensorData[sensorId][index]["Timestamp"]*1000),y:parseFloat(SensorData[sensorId][index]["Temperature"]) });
            
           });
          
      });

 
      //console.log(Series);


  
    chart.render();
  }

  window.onload = function(){updateChart()};
  //setInterval(function(){updateChart()},1000);

  </script>
 <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>





  </body>
</html>
