<?php

if (isset($_GET['p']) && is_numeric($_GET['p'])) {
  $p = $_GET['p'];
} else {
  $p = '1';
}

$access_token = '5cc9b44d2ede130604768726b88ab83dc64a7624df52fa92c7ba08227df5a3df';

$url = 'https://gis-api.aiesec.org/v2/opportunities.json?access_token='.$access_token.'&filters[programmes][]=1&filters[home_mcs][]=1552&page='.$p.'&filters[last_interaction][from]=2017-01-30&filters[earliest_start_date]=2018-2-8';

// $json = CallAPI('GET', $url);
$json = CallAPIget($url);
$data = json_decode($json, true);

if (isset($data['error']) && isset($data['error']) == 'page is invalid') {
  header("Location: /aiesec/?p=1");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="icon" type="image/x-png" href="./img/favicon.png">
  <title>AIESEC API</title>
</head>
<body>

<h1 class="text-center heading">All Opportunities From Morocco!</h1>

<div class="container">
  <div class="row">

<?php

if (isset($data['data'])) {
  foreach ($data['data'] as $key => $value) {
    $opp_id = 'https://aiesec.org/opportunity/' . $value['id'];
    $opp_image = $value['cover_photo_urls'];
    $opp_duration = $value['duration'];
    $opp_title = $value['title'];

    $opp_location = explode(",", $value['location']);
    $opp_location = $opp_location[0];

    // Some locations are not set, use city instead
    if ($opp_location == "") {
      $opp_location = explode(",", $value['city']);
      $opp_location = $opp_location[0];
    }

    $code = '
    <div class="col-sm-12 col-md-6 col-lg-4">
      <div class="card">
        <div class="card-img-top">
          <img class="" src="'.$opp_image.'" alt="'.$opp_title.'">
        </div>
        <div class="card-body">
          <h5 class="card-title">'.$opp_title.'</h5>
          <div class="card-text">
            <p>'.$opp_duration . ' WEEK</p>
            <p><strong>City:</strong> '.$opp_location . '</p>
          </div>
          <a href="'. $opp_id .'" target="_blank" class="btn btn-primary">Apply</a>
        </div>
      </div>
    </div>
    ';

    echo $code;  
  }
} elseif (isset($data['status']['code']) && $data['status']['code'] == 401) {
  echo 'Refresh access token';
} elseif (isset($data['error'])) {
  echo $data['error'];
} else {
  echo var_dump($data);
}

function CallAPIget($url) {
  $curl = curl_init();
  
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  curl_close($curl);

  return $result;
}

function CallAPI($method, $url, $data = false) {
    
  $curl = curl_init();

  switch ($method) {
    case "POST":
      curl_setopt($curl, CURLOPT_POST, 1);

      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

      break;
    case "PUT":
      curl_setopt($curl, CURLOPT_PUT, 1);
      break;
    default:
      if ($data)
        $url = sprintf("%s?%s", $url, http_build_query($data));
  }

  // Optional Authentication:
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($curl, CURLOPT_USERPWD, "username:password");

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  curl_close($curl);

  return $result;
}

?> 

  </div>
  <?php
    if (isset($data['paging']['current_page'])) {
      $cp = $data['paging']['current_page'];
      $tp = $data['paging']['total_pages'];
      $activep = '';
      $activen = '';
      if ($cp == 1) {
        $activep = ' disabled';
      }
      if ($cp == $tp) {
        $activen = ' disabled';
      }
    } else {
      $cp = 2;
      $tp = 1;
    }
  ?>
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <li class="page-item<?=$activep?>"><a class="page-link" href="./?p=<?= $cp - 1; ?>">Previous</a></li>
      <?php
        for ($i = 1; $i <= $tp; $i++) {
          $activetab = '';
          if ($i == $cp) {
            $activetab = ' active';
          }
          $link = '<li class="page-item'.$activetab.'"><a class="page-link" href="./?p='.$i.'">'.$i.'</a></li>';
          echo $link;
        }
      ?>
      <li class="page-item<?=$activen?>"><a class="page-link" href="./?p=<?= $data['paging']['current_page'] + 1; ?>">Next</a></li>
    </ul>
  </nav>
</div>
</body>
</html>
