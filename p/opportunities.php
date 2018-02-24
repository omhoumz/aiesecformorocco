<?php
include('../incs/funcs.php');
include('../incs/access_token.php');

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
if (isset($_GET['mc']) && is_string($_GET['mc']) && (array_key_exists($_GET['mc'], $mcs) || in_array($_GET['mc'], $mcs)) ) {
  $q_mc = $_GET['mc'];
} else {
  $q_mc = 'brazil';
}

// api call

$mc_filter = $mcs[$q_mc];

$url = 'https://gis-api.aiesec.org/v2/opportunities.json?access_token='.$access_token.'&filters[programmes][]=1&filters[home_mcs][]='.$mc_filter.'&page='.$p.'&filters[last_interaction][from]=2017-01-30&filters[earliest_start_date]=2018-2-16&per_page=26';

// $json = CallAPI('GET', $url);
$json = CallAPIget($url);
$data = json_decode($json, true);

if (isset($data['error']) && isset($data['error']) == 'page is invalid') {
  header("Location: /aiesec/p/opportunities.php?p=1&mc=brazil");
  exit();
}

// get opportunities count
$opp_count = $data['paging']['total_items'];

?>

<?php
include('../elts/head.php');
?>
<body id="top">
<?php
include('../elts/navbar.php');
?>


	<section class="section section-light section-opportunities hm">
		<div class="section-heading text-center">
      <h2>Opportunities From 
        <strong>
          <div class="dropdown show d-inline">
            <a class="btn btn-light text-primary btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?=ucfirst($q_mc)?>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <?php
                foreach ($mcs as $k => $v) {
                  $link = '<a class="dropdown-item" href="./opportunities.php?p=1&mc='.$k.'">'. ucfirst($k) .'</a>';
                  if ($k != 'morocco' && $k != $q_mc) {
                    echo $link;
                  }
                }
              ?>
            </div>
          </div>!
        </strong>
        <small class="text-muted"><?=$opp_count?> opportunties found.</small>
      </h2>
		</div>
		<div class="section-body container">

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
                  <div class="card-img-top" style="background-image:url('.$opp_image.')">
                    <div class="opportunity-image-overlay GV-overlay"></div>
                    <div class="opportunity-image-bottom-overlay"></div>
                    <div class="brand-label">
                      <div class="global-logo gv-en"></div>
                    </div>
                    <img class="card-img-hide" src="'.$opp_image.'" alt="'.$opp_title.'">
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

        <nav class="pagination-dropdown">
          <a href="./opportunities.php?p=<?= $cp - 1; ?>&mc=<?=$q_mc?>" class="btn btn-light text-primary d-inline<?=$activep?>">Previous</a>

          <div class="dropdown d-inline">
            <button class="btn btn-light text-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Go To Page (<?=$cp?>)
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <?php
                for ($i = 1; $i <= $tp; $i++) {
                  $activetab = '';
                  if ($i == $cp) {
                    $activetab = ' active';
                  }
                  $link = '<li class="page-item'.$activetab.'"><a class="page-link" href="./opportunities.php?p='.$i.'&mc='.$q_mc.'">'.$i.'</a></li>';
                  echo $link;
                }
              ?>
            </div>
          </div>
          
          <a href="./opportunities.php?p=<?=$cp + 1;?>&mc=<?=$q_mc?>" class="btn btn-light text-primary d-inline<?=$activen?>">Next</a>
        </nav>

    </div>
  </section>

<?php
include('../elts/footer.php');
?>
<?php
include('../elts/inc_js.php');
?>

</body>
</html>
