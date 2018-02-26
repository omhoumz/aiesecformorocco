<header class="small-header header-section">
  <div class="inner-header">
    <div class="darken-bg"></div>
    <div class="headerImage">
      <div class="container">
        <h2 class="text-shadow display-3 heading">
          <?php
          if (isset($_GET['p-heading']) && is_string($_GET['p-heading'])){
            echo $_GET['p-heading'];
          } else {
            echo 'AIESEC, a leadership reference';
          }
          ?>
        </h2>
      </div>
    </div>
  </div>
</header>