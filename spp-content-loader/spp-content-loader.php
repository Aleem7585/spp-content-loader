<?php
/*
Plugin Name: Bulk Articles Importer
Plugin URI: http://bestplrproducts.com/
Description: A mass text articles importer. Great for PLR or quickly uploading lots of posts.
Version: 9.0.1.1
Author: BestPLRProducts
Author URI: http://bestplrproducts.com/
Stable tag: 9.0.1.1
*/
// Define constants

register_activation_hook( __FILE__, 'my_plugin_create_db' );
function my_plugin_create_db() {


    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'Article_import';


    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        Article_name varchar(250) NOT NULL,
        artical_import_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        Total_article int(11) NOT NULL,
        Imported_article int(11) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";


    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook(__FILE__, 'createTable');

define( 'TBP_GULP_DIR', plugins_url( '', __FILE__ ));
define( 'TBP_GULP_PATH', dirname( __FILE__) );
define( 'TBP_GULP_TMP', get_temp_dir() );
define( 'TBP_GULP_PLUGIN_NAME', 'Abundant Content Loader' );



add_action('admin_menu','bp_admin_menu');
add_action('admin_init','bp_admin_init');
//add_action('admin_head','bp_admin_head');






$pluginPath = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__));
    function bp_admin_init() {
    require_once(ABSPATH . 'wp-admin/includes/admin.php');
    
    /*
     Temporary path defaults to WordPress's temp path, WP_TEMP_DIR, which may not be
     defined. If not defined, WordPress will then see if WP_CONTENT_DIR (wp-content)
     is writable. If it cannot write to wp-content, it will then try to write to the
     systemwide tmp dir. If that fails, usually in the case of an overly restrictive
     open_basedir directive, you will have to change the path below to one that is
     writable by the http process, and readable according to PHP's basedir directive
     All files written by this plugin are temporary and will automatically be removed
     after they are processed. Plugin will also only accept files ending in .zip, to
     prevent any executable code from being uploaded in the first place.
    */
    
    $tmpPath = get_temp_dir();
	}



function insert_data($article_name,$articlecount){

     global $wpdb;


    $tablename=$wpdb->prefix.'article_import';

    $data=array(
        'Article_name' => $article_name,
        'artical_import_date' =>  date("Y-m-d H:i:s"),
        'Total_article' =>  $articlecount,
        'Imported_article' => $articlecount
        );


     $wpdb->insert( $tablename, $data);
}

function bp_admin_head() {
    wp_enqueue_script('bp_lib',TBP_GULP_DIR.'/js/lib.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ));
    wp_enqueue_script('jquery-ui-timepicker-addon',TBP_GULP_DIR.'/js/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ));
	
	// styles
	wp_enqueue_style( 'datepickerstyles', TBP_GULP_DIR.'/js/jquery.ui.datepicker.css' );
	wp_enqueue_style( 'timepickerstyles', TBP_GULP_DIR.'/js/jquery-ui-timepicker-addon.css' );
	wp_enqueue_style( 'jqueryuiallstyles', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/pepper-grinder/jquery-ui.css' );
}

function bp_admin_menu() {
    // Add new top level admin menu
	$menupg = add_menu_page(TBP_GULP_PLUGIN_NAME, TBP_GULP_PLUGIN_NAME, 'manage_options', __FILE__, 'bp_plugin_admin');
  add_submenu_page(__FILE__, 'Imported Articles', 'Imported Articles', 'manage_options', __FILE__.'/custom', 'clivern_render_custom_page');

	// Enqueues JS only on our page
	add_action( 'load-'.$menupg, 'bp_admin_head' );


  
}

function clivern_render_custom_page(){
   ?>
   <div class='wrap'>
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
   </div>
   <?php
 }


function bp_plugin_admin() {
    // Print upload form
    if(isset($_FILES['zip'])) {
        bp_process_upload();
    }

    elseif(isset($_POST['zipfile']) && $_POST['post_status'][0] == 2) {
       bp_process_zip_now();

    	//echo "publish";
    }
    elseif(isset($_POST['zipfile'])) {
        bp_process_zip();

    	//echo $_POST['post_status'][0];
    }
    else {
        bp_show_upload_form();
    }
}

function bp_get_post_type($num) {
	?>
	<select name="post_type[<?php echo $num; ?>]" class="default_post_type">
		<option value="post">Post</option>
		<option value="page">Page</option>
		<?php 
		$args=array(
		  'public'   => true,
		  '_builtin' => false
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types=get_post_types($args,$output,$operator); 
		  foreach ($post_types  as $post_type ) { ?>
			<option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
		<?php
		  }
		?>
		</select>
	<?php
}

function tbp_retrieve_post_type() {
  ?>
  <select name="post_type[<?php echo $num; ?>]" class="default_post_type">
    <option value="post">Post</option>
    <option value="page">Page</option>
    <?php 
    $args=array(
      'public'   => true,
      '_builtin' => false
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
      foreach ($post_types  as $post_type ) { ?>
      <option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
    <?php
      }
    ?>
    </select>
  <?php
}

function bp_process_zip() {
    $zip = zip_open($_POST['zipfile']) or die("could not reopen zip");
        
    // convert date/time to numbers
        $date = strtotime($_POST['start_date']);
        $time = strtotime($_POST['start_time']);
        $combined_date = date('Y-m-d',$date) . ' ' . date('H:i:s',$time);
        $next_post = mysql2date('U',$combined_date);
   
    $interval = $_POST['post_interval'] * 60 * 60;
    
    $num = 0;
    ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php echo TBP_GULP_PLUGIN_NAME; ?></h2>
    <div id="poststuff" class="metabox-holder">

      <div id="post-body-content" class="">
        <div class="postbox">
        <h3 class="hndle"><span>Processing Entries</span></h3>
        <div class="inside">
    <?php
    while($zip_entry = zip_read($zip)) {
        zip_entry_open($zip,$zip_entry);
        $zip_entry_size = zip_entry_filesize($zip_entry);
        $filename = zip_entry_name($zip_entry);
        $ext = substr($filename, strrpos($filename, '.') + 1);
    
    	if(strtolower($ext) != 'txt') {
        	continue;
    	}
    
        $text = iconv("CP1252","UTF-8",zip_entry_read($zip_entry,$zip_entry_size));        
        $text = preg_split("[\n\r]",$text,2);
        
		// Set $data as array
        $data = Array();
        
		// post_status conditions
		switch($_POST['post_status'][$num]) {
            case 0:
                $data['post_status'] = 'publish';
                break;
            case 1:
                $data['post_status'] = 'draft';
                break;
            default:
                $data['post_status'] = 'error';
                break;
        }
        
		// The rest of the post $data
		$data['post_type'] = $_POST['post_type'][$num];
        $data['post_author'] = $_POST['post_author'][$num];
        $data['post_category'] = array($_POST['post_category'][$num]);
        //$data['post_title'] = $text[0];
		$data['post_title'] = wp_kses_post( $_POST['post_title'][$num] );
        $data['post_content'] = wp_kses_post( $text[1] );
         $data['post_date'] = date('Y-m-d H:i:s', $next_post);
         $data['post_date_gmt'] = get_gmt_from_date($data['post_date']);
        
        wp_insert_post($data);
        echo '<ul>';
        if($data['post_type'] == 'page') {
            echo "<li><strong>Page</strong> ".$data['post_title']." successfully created.</li>";
        } else {
            echo "<li>Article ".$data['post_title']."<br>successfully set to be posted with status ".$data['post_status']." at ".$data['post_date'].".</li>";
        }	
        echo '</ul>';
        zip_entry_close($zip_entry);
        $num++;
        $next_post += $interval;
    } //endwhile
  //  header('location:CustomPlugin/wp-admin/edit.php');
    echo "<strong>$num articles successfully added.</strong>"; ?>
        </div>
      </div>
    </div>
    </div>
    </div>
    <?php
    zip_close($zip);
    unlink($_POST['zipfile']);
    
}

function bp_process_zip_now() {
    $zip = zip_open($_POST['zipfile']) or die("could not reopen zip");
        
    // convert date/time to numbers
        $date = strtotime($_POST['start_date']);
        $time = strtotime($_POST['start_time']);
        $combined_date = date('Y-m-d',$date) . ' ' . date('H:i:s',$time);
        $next_post = mysql2date('U',$combined_date);
   
    $interval = $_POST['post_interval'] * 60 * 60;
    
    $num = 0;
    ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php echo TBP_GULP_PLUGIN_NAME; ?></h2>
    <div id="poststuff" class="metabox-holder">

      <div id="post-body-content" class="">
        <div class="postbox">
        <h3 class="hndle"><span>Processing Entries</span></h3>
        <div class="inside">
        	</div>
      </div>
    </div>
    </div>
    </div>
    <?php
    while($zip_entry = zip_read($zip)) {
        zip_entry_open($zip,$zip_entry);
        $zip_entry_size = zip_entry_filesize($zip_entry);
        $filename = zip_entry_name($zip_entry);
        $ext = substr($filename, strrpos($filename, '.') + 1);
    
    	if(strtolower($ext) != 'txt') {
        	continue;
    	}
    
        $text = iconv("CP1252","UTF-8",zip_entry_read($zip_entry,$zip_entry_size));        
        $text = preg_split("[\n\r]",$text,2);
        
		// Set $data as array
        $data = Array();
        
		// post_status conditions
		switch($_POST['post_status'][$num]) {
            case 0:
                $data['post_status'] = 'publish';
                break;
            case 1:
                $data['post_status'] = 'draft';
                break;
                  case 2:
                $data['post_status'] = 'Publish';
                break;
            default:
                $data['post_status'] = 'error';
                break;
        }
        
		// The rest of the post $data
		$data['post_type'] = $_POST['post_type'][$num];
        $data['post_author'] = $_POST['post_author'][$num];
        $data['post_category'] = array($_POST['post_category'][$num]);
        //$data['post_title'] = $text[0];
		$data['post_title'] = wp_kses_post( $_POST['post_title'][$num] );
        $data['post_content'] = wp_kses_post( $text[1] );
        $data['post_status'] = "publish";
        // $data['post_date_gmt'] = get_gmt_from_date($data['post_date']);
        
        wp_insert_post($data);

        // echo '<ul>';
        // if($data['post_type'] == 'page') {
        //     echo "<li><strong>Page</strong> ".$data['post_title']." successfully created.</li>";
        // } else {
        //     echo "<li>Article ".$data['post_title']."<br>successfully set to be posted with status ".$data['post_status']." at ".$data['post_date'].".</li>";
        // }	
        // echo '</ul>';
        zip_entry_close($zip_entry);
        $num++;
        $next_post += $interval;
    }
     //endwhile
   // header(location:'admin_url()/edit.php');
      // header('location:CustomPlugin/wp-admin/edit.php');
   // echo "<strong>$num articles successfully added.</strong>";
    
    zip_close($zip);
    unlink($_POST['zipfile']);
    ?>
   
	 <script type="text/javascript">
    	 location.replace('edit.php')
    </script>
    <?php
}
function bp_process_upload() {
    global $tmpPath;
    if( empty( $tmpPath ) ) {
          $upload_dir = wp_upload_dir();
          $tmpPath = $upload_dir['path'];
        }
    global $wpdb;
    
    
    wp_enqueue_script('jquery');
    
    // grab the lists once so we're not hitting the database hundreds/thousands of times.
    $authors = wp_dropdown_users('echo=0&name=RPLCME');
    $categories = wp_dropdown_categories('echo=0&hide_empty=0&hierarchical=1&name=RPLCME');
    $statuses = '<select name="RPLCME" id="RPLCME"><option value="0">Scheduled</option><option value="1">Draft</option><option value="2">Publish</option></select>';
    if(!is_uploaded_file($_FILES['zip']['tmp_name'])) { echo "<PRE>"; print_r($_FILES); echo "</PRE>"; die("Upload failed?"); }
    
    $zipFile = $tmpPath . $_FILES['zip']['name'];
    move_uploaded_file($_FILES['zip']['tmp_name'], $zipFile);
    
    $zip = zip_open($zipFile);
    
    if(!$zip) {
        unlink($zipFile);
        die("Failed to open file. Is it really a ZIP file?");
    }
    
    ?>    
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php echo TBP_GULP_PLUGIN_NAME; ?></h2>
    <div id="poststuff" class="metabox-holder">
      <div id="post-body-content" class="">
        <div class="postbox">
        <h3 class="hndle"><span>How Do You Want The Articles To Be Imported?</span></h3>
        <div class="inside">         
        <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="POST">            
          <table>                
           <tr>
                  <td colspan="3">Select the article import options. You can make all articles publish in one post or individually<br /></td>
                </tr>
           <tr>
            <td>Date/time to start posting</td>
            <td><input type="text" class="date_input" id="start_date" name="start_date" value="<?php echo date('d M Y');?>" /></td>
            <td><input type="text" class="time_input" id="start_time" name="start_time" value="<?php echo date('g:m A');?>" /></td>
          </tr>
          <tr>
            <td>Interval to post at (in hours)</td>
            <td colspan="2"><input type="text" name="post_interval" value="24" /></td>
          </tr>
          <tr>
            <td>Set all articles to this author</td>
            <td><?php echo str_replace('RPLCME','default_author',$authors)?></td>
            <!-- <td><input type="button" onclick="setAllAuthors();" value="Apply" /></td> -->
          </tr>
          <tr>
            <td>Set all articles to this category</td>
            <td><?php echo str_replace('RPLCME','default_category',$categories)?></td>
            <!-- <td><input type="button" onclick="setAllCategories();" value="Apply" /></td> -->
          </tr>
          <tr>
            <td>Set all articles to this status</td>
            <td>
            <input type="button" onclick="setAllStatus(0);" value="Scheduled" />                    
            <input type="button" onclick="setAllStatus(1);" value="Draft" />

            <input type="button" onclick="setAllStatus(2);" value="Publish" />
            </td>
          </tr>
		  <tr>
		  	<!-- <td>Set all articles to this post type</td> -->
		  	<td>
			<!-- <?php tbp_retrieve_post_type(); ?> -->
			</td>
			<!-- <td><input type="button" onclick="setAllPostType();" value="Apply" /></td> -->
		  </tr>
        </table>            <br /><br />
        <table width="95%">
          <tr>
            <td>Article</td>
            <td>Author</td>
            <td>Category</td>
            <td>Status</td>
			<td>Post Type</td>
          </tr>    
<?php 
    
    $num = 0;
    
    while($zip_entry = zip_read($zip)):
    zip_entry_open($zip,$zip_entry);
    $filename = zip_entry_name($zip_entry);
    $ext = substr($filename, strrpos($filename, '.') + 1);
    
    if(strtolower($ext) != 'txt') {
        continue;
    }
    
    $text = iconv("CP1252","UTF-8",zip_entry_read($zip_entry,1024));
    
    $lines = preg_split("[\n\r]",$text,2);
    
    $title = $lines[0];  
    ?>
    
    <tr>
      <td><input type="text" name="post_title[<?php echo $num; ?>]" class="regular-text" value="<?php echo $title; ?>"></td>
      <td><?php
           echo str_replace('RPLCME','post_author[' . $num . ']',$authors);
           ?>
      </td>
      <td><?php
            echo str_replace('RPLCME','post_category[' . $num . ']',$categories);
            ?>
      </td>
      <td><?php
           echo str_replace('RPLCME','post_status[' . $num . ']',$statuses);
           ?>
      </td>
		<td><?php
			bp_get_post_type($num);
			?>
		</td>
    </tr>
             
    <?php
    zip_entry_close($zip_entry);
    $num++;
    endwhile;
    zip_close($zip);
    insert_data($_FILES['zip']['name'],$num);
    ?>             
    <tr>
      <td colspan="4">
        <p>
          <input type="submit" class="button-primary" value="Process Articles" />
        </p>
    </tr>            
  </table>            
  <input type="hidden" name="zipfile" value="<?php echo $zipFile?>" />
          
</form>
        </div>
       </div>
    </div>
    </div>
    </div>
<?php
}
function bp_show_upload_form() {
        global $tmpPath;
        if( empty( $tmpPath ) ) {
          $upload_dir = wp_upload_dir();
          $tmpPath = $upload_dir['path'];
        }
    ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php echo TBP_GULP_PLUGIN_NAME; ?></h2>
    <div id="poststuff" class="metabox-holder">
      <div id="post-body-content" class="">
        <div class="postbox">
        <h3 class="hndle"><span>Upload ZIP File</span></h3>
        <div class="inside">
        <p>Click Browse to get the ZIP file you want to upload, then click Upload</p>        
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" enctype="multipart/form-data">            
  <label for="zip">Upload Zip
  </label>            
  <input type="file" name="zip" />
  <p>
    <input type="submit" class="button-primary" value="Upload Zip for Processing" />
  </p>
</form>
        </div>
      </div>
    </div>
    </div>
    </div>
<?php
}
?>
