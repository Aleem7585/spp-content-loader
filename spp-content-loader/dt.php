<?php
/**
 * Edit Posts Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );


require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap">
<h1 class="wp-heading-inline">

	Article Import Details
	</h1> 
	  <div id="post-body-content" class="">
        <div class="postbox">
       <!--  <h3 class="hndle"><span>Processing Entries</span></h3> -->
        <div class="inside">
	<table border="1" style="height: 200px;width: 500px;">
<tr>
 
 <th>Artcle Name</th>
 <th>Article import date</th>
  <th>Total Article</th>
   <th>Imported Article</th>
</tr>
  <?php
    global $wpdb;
    $result = $wpdb->get_results ( "SELECT * FROM wp_article_import" );

    foreach ( $result as $print )   {
    ?>
    <tr>
    <td><?php echo $print->Article_name;?></td>
     <td><?php echo $print->artical_import_date;?></td>
     <td><?php echo $print->Total_article;?></td>
      <td><?php echo $print->Imported_article;?></td>
    </tr>
        <?php }
  ?>          
</table>
</div>
</div>
</div>
</div>

</h1>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
