<?php
include('../elts/head.php');
?>

<body id="top">

<?php
include('../elts/navbar.php');
?>

<section class="section section-light section-faq hm">
  <div class="section-heading text-center">
    <h2>FAQ</h2>
  </div>

  <div class="section-body container">

    <div id="accordion">

    <?php
    // Read JSON file
    $json = file_get_contents('../data/qs.json');

    //Decode JSON
    $json_data = json_decode($json, true);

    if (isset($json_data)) {
      foreach ($json_data as $k => $v) {
        $question = $v['q'];
        $answer = $v['a'];
        $b = '';
        $d = '';
        if ($k == 0) {
          $b = '<button class="btn btn-link" data-toggle="collapse" data-target="#collapse'.$k.'" aria-expanded="true" aria-controls="collapse'.$k.'">';
          $d = '<div id="collapse'.$k.'" class="collapse show" aria-labelledby="heading'.$k.'" data-parent="#accordion">';
        } else {
          $b = '<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse'.$k.'" aria-expanded="false" aria-controls="collapse'.$k.'">';
          $d = '<div id="collapse'.$k.'" class="collapse" aria-labelledby="heading'.$k.'" data-parent="#accordion">';
        }
        $card_code = '
          <div class="card">
            <div class="card-header" id="heading'.$k.'">
              <h5 class="mb-0">
                '.$b.'
                '.$question.'
                </button>
              </h5>
            </div>
            
            '.$d.'
              <div class="card-body">
              '.$answer.'
              </div>
            </div>
          </div>
        ';
        echo($card_code);
      }
    }
    ?>
    </div>
  </div>
</section>

<?php
include('../elts/footer.php');
include('../elts/inc_js.php');
?>

</body>
</html>