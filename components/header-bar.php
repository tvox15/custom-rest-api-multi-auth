<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">[REMOVED]</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">

    </ul>
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) : ?>
      <?php if($_SESSION["userType"] === "C"): ?>
        <a class="btn btn-outline-success my-2 my-sm-0" href="<?=$homePath;?>admin.php">Admin</a>
      <?php endif;?>
      <a class="btn btn-outline-success my-2 my-sm-0" href="<?=$homePath;?>auth/logout.php">Logout</a>
      <?php else :?>
        <a class="btn btn-outline-success my-2 my-sm-0" href="<?=$homePath;?>auth/login.php">Login</a>
        <a class="btn btn-outline-success my-2 my-sm-0" href="<?=$homePath;?>auth/register.php">Register</a>
      <?php endif;?>
  </div>
</nav>


<script>


</script>