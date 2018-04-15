<?php
include('../elts/head.php');
?>

<body id="top">

<?php
include('../elts/navbar.php');
$_GET['p-heading'] = 'AIESEC for Organizations';
include('../elts/small-header.php');
?>

<section class="section section-light section-partner-us hm" id="section-partners">
  <div class="section-heading text-center"><h2>Partner With Us</h2></div>
  <div class="section-body container body-partner-us">

    <h4 class="text-center"><a href="mailto:contact@aiesec.ma" class="display-4 text-dark" target="_blank">contact@aiesec.ma</a></h4>
    <h4 class="text-center"><a href="https://www.facebook.com/AIESECMorocco/" class="display-4 text-dark" target="_blank">Facebook Page</a></h4>

  </div>
</section>

<!-- Uncomment next section to reviel partners in /data/partners.json -->
<!-- <section class="section section-light section-partners hm" id="section-partners">
  <div class="section-heading text-center"><h2>Our Partners</h2></div>
  <div class="section-body container body-partners">
    <?php
      // // Read JSON file
      // $json_partners = file_get_contents('../data/partners.json');

      // //Decode JSON
      // $partners_data = json_decode($json_partners, true);

      // if (isset($partners_data)) {
      //   foreach ($partners_data as $v) {
      //     $div_code = '<div class="partner-item">
      //       <img src="'.$v['p_logo'].'" alt="'.$v['p_name'].'">
      //     </div>';
      //     echo $div_code;
      //   }
      // }
    ?>
  </div>
</section> -->

<?php
include('../elts/footer.php');
include('../elts/inc_js.php');
?>

</body>
</html>