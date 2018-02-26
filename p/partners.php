<?php
include('../elts/head.php');
?>

<body id="top">

<?php
include('../elts/navbar.php');
$_GET['p-heading'] = 'AIESEC for Organizations';
include('../elts/small-header.php');
?>

<section class="section section-light section-partners" id="section-partners">
  <div class="section-heading text-center"><h2>Our Partners</h2></div>
  <div class="section-body container body-partners">
    <?php
      // Read JSON file
      $json_partners = file_get_contents('../data/partners.json');

      //Decode JSON
      $partners_data = json_decode($json_partners, true);

      if (isset($partners_data)) {
        foreach ($partners_data as $v) {
          $div_code = '<div class="partner-item">
            <img src="'.$v['p_logo'].'" alt="'.$v['p_name'].'">
          </div>';
          echo $div_code;
        }
      }
    ?>
  </div>
</section>

<?php
include('../elts/footer.php');
include('../elts/inc_js.php');
?>

</body>
</html>