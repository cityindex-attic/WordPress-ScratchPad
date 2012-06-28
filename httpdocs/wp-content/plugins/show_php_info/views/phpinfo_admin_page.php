<div class="wrap">
<?php 
function strip_out_styles($buffer)
{
  return preg_replace('/<style.*style>/', '', $buffer);
}

ob_start("strip_out_styles");

	phpinfo();

ob_end_flush();

?>
</div>