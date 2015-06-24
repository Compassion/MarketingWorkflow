<?php 
// Set Some Vars
$backlogCount = returnStatusCount('Backlog', 'none');
$queryCount = returnStatusCount('Query', $_SESSION['user_group']);
$scopeCount = returnStatusCount('Scoped', 'none');
$approveCount = returnStatusCount('Approved', 'none');
$pendingCount = returnStatusCount('Pending', 'none');
?>

<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">Workflow</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
      <ul class="nav navbar-nav">
        <?php displayNavMenu($_SESSION['user_group'], $backlogCount, $queryCount, $scopeCount, $approveCount, $pendingCount); ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="index.php?logout" class="">Log Out</a></li>
      </ul>
        
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>