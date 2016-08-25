<?php
include('header.php');
$start = $general->start_period();
if ($start != 0) {
if (!isset($_GET['month']) OR empty($_GET['month']) OR $_GET['month'] > 12) {$current_month = date('n');} else {$current_month = intval($_GET['month']);}
if (!isset($_GET['year']) OR empty($_GET['year']) OR $year > date('Y')) {$current_year = date('Y');} else {$current_year = intval($_GET['year']);}
?>
<div class="page-header page-heading">
	<div class="row">
	<div class="col-md-9"><h1><i class="fa fa-bar-chart"></i> News Statistics For <span class="text-info"><?php echo month_name($current_month).', '.$current_year; ?></span></h1></div>
	<div class="col-md-3">
	<form method="GET" name="menu">
	<select name="selectedPage" onChange="changePage(this.form.selectedPage)" class="form-control">
	<option>Choose a Month</option>
	<?php
	echo generate_statics_select($start['year'],$start['month']);
	?>
	</select>
	</form>
	</div>
	</div>
</div>
<div class="row">
<?php
$thetime = mktime(0, 0, 0, $current_month, 3, $current_year);
$days = date('t',$thetime);
?>
<script>
						$(function() {
						Morris.Bar({
							element: 'morris-area-chart',
							data: [
							<?php for($i=1;$i<$days+1;$i++) 
							{ 
							?>
							<?php echo "{"; ?>
							periods: '<?php echo $current_year.'-'.$current_month.'-'.$i; ?>',
							news: <?php echo $general->statistics_news($i,$current_month,$current_year); ?>
							<?php echo "}, "; ?>
							<?php } ?>
							],
							xkey: 'periods',
							ykeys: ['news'],
							labels: ['News'],
							barColors: ['#61A9DC'],
							pointSize: 4,
							hideHover: 'auto',
							resize: true
						});
						});
						</script>
						<div id="morris-area-chart"></div>
						</div>
<?php
}
?>
<div class="page-header page-heading">
<h1>Cronjob Command</h1>
</div>
<p>Replace <b>YOUR-ACCOUNT</b> with your real hostname</p>
<div class="alert alert-warning"><code>php -q /home/YOUR-ACCOUNT/include/cron.php</code></div>
<?php
include('footer.php');
?>