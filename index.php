<?php

// Getting parameters

  // page number
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
  $p = $_GET['p'];
} else {
  $p = '1';
}

  // queried MC
    // supported MCs
    // 1552 ==> Morocco
    // 1606 ==> Brazil
    // 1609 ==> Egypt
    // 1622 ==> turkey
$mcs = [
  "morocco" => "1552",
  "brazil" => "1606",
  "egypt" => "1609",
  "turkey" => "1622"
];
if (isset($_GET['mc']) && is_string($_GET['mc']) && array_key_exists($_GET['mc'], $mcs)) {
  $q_mc = $_GET['mc'];
} else {
  $q_mc = 'brazil';
}

// api call
$access_token = '3cc7a2dd52568c2bcc586c9d425e33e5e340d62eea1ee9de541c58eb8791328f';

$mc_filter = $mcs[$q_mc];

$url = 'https://gis-api.aiesec.org/v2/opportunities/search.json?access_token='.$access_token.'&filters[programmes][]=1&filters[home_mcs][]='.$mc_filter.'&page='.$p.'&filters[last_interaction][from]=2017-01-30&filters[earliest_start_date]=2018-2-16';

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

<h1 class="text-center heading">Opportunities From <strong><?php echo ucfirst($q_mc); ?></strong>!</h1>

<h4 class="text-center heading">See also from 
  <div class="dropdown show">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    This List
    </a>

    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item" href="http://localhost/aiesec/?mc=brazil">Brazil</a>
      <a class="dropdown-item" href="http://localhost/aiesec/?mc=egypt">Egypt</a>
      <a class="dropdown-item" href="http://localhost/aiesec/?mc=turkey">Turkey</a>
    </div>
  </div>
</h4>

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
      <li class="page-item<?=$activep?>"><a class="page-link" href="./?p=<?= $cp - 1; ?>&mc=<?=$q_mc?>">Previous</a></li>
      <?php
        for ($i = 1; $i <= $tp; $i++) {
          $activetab = '';
          if ($i == $cp) {
            $activetab = ' active';
          }
          $link = '<li class="page-item'.$activetab.'"><a class="page-link" href="./?p='.$i.'&mc='.$q_mc.'">'.$i.'</a></li>';
          echo $link;
        }
      ?>
      <li class="page-item<?=$activen?>"><a class="page-link" href="./?p=<?=$cp + 1;?>&mc=<?=$q_mc?>">Next</a></li>
    </ul>
  </nav>
</div>

	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
