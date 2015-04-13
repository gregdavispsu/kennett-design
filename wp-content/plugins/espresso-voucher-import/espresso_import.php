<?php
/*
  Plugin Name: Event Espresso - Voucher Import
  Plugin URI: http://eventespresso.com/
  Description: Allows the import of attndees into Event Espresso

  Version: 0.1

  Author: Event Espresso
  Author URI: http://www.eventespresso.com

  Copyright (c) 2008-2012 Event Espresso  All Rights Reserved.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

function espresso_voucher_import() {
	$wp_plugin_url = WP_PLUGIN_URL;
	$wp_content_url = WP_CONTENT_URL;
	define("ESPRESSO_VOUCHER_IMPORT_PLUGINPATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
	define("ESPRESSO_VOUCHER_IMPORT_PLUGINPATH", WP_PLUGIN_DIR . ESPRESSO_VOUCHER_IMPORT_PLUGINPATH);
	define("ESPRESSO_VOUCHER_PLUGINFULLURL", $wp_plugin_url . ESPRESSO_VOUCHER_IMPORT_PLUGINPATH);
	
	
?>
    <h3>Voucher Import</h3>
    <ul>
        <li>
            <p>This page is for importing your voucher codes from a comma separated file (CSV) directly into the the database.</p>
            <p style=" font-weight:bold">Usage:</p>
			<ol>
                <li>I have included a template file <a href="<?php echo ESPRESSO_VOUCHER_PLUGINFULLURL; ?>vouchers.csv">here</a> that I recommend you download and use.  It is very easy to work with it in excel, just remember to save it as a csv and not excel sheet.</li>
                <li>The file name should be vouchers.csv in order for it to work. I will fix this issue later, I just wanted to get this working first.</li>
				<li>One final note, you will see that the header row, first column has a 0 while other rows have a 1.  This tells the upload to ignore rows that have the 0 identifier and only use rows with the 1.</li>
            </ol>
           
            <?php
			$success_messages = '';
			$error_messages = '';
			voucher_uploader( 1, array("csv"), 1048576, '../wp-content/uploads/espresso/', $success_messages, $error_messages );
            ?>
        </li>
    </ul>
<?php
}

/*
  uploader([int num_uploads [, arr file_types [, int file_size [, str upload_dir ]]]]);

  num_uploads = Number of uploads to handle at once.

  file_types = An array of all the file types you wish to use. The default is txt only.

  file_size = The maximum file size of EACH file. A non-number will results in using the default 1mb filesize.

  upload_dir = The directory to upload to, make sure this ends with a /
 */

function voucher_uploader($num_of_uploads = 1, $file_types_array = array("csv"), $max_file_size = 1048576, $upload_dir = "../wp-content/uploads/espresso/", $success_messages, $error_messages) {

	
    if (!is_numeric($max_file_size)) {
        $max_file_size = 1048576;
    }
    if (!isset($_POST["submitted"])) {
        $form = "<form action='admin.php?page=espresso_voucher_import&action=voucher_import' method='post' enctype='multipart/form-data'><p>Upload files:</p><input type='hidden' name='submitted' value='TRUE' id='" . time() . "'><input name='action' type='hidden' value='voucher_import' /><input type='hidden' name='MAX_FILE_SIZE' value='" . $max_file_size . "'>";
        for ($x = 0; $x < $num_of_uploads; $x++) {
            $form .= "<p><font color='red'>*</font><input type='file' name='file[]'>";
        }
        $form .= "<input class='button-primary' type='submit' value='Upload Vouchers'></p></form>";
        echo($form);
    } else {
        foreach ($_FILES["file"]["error"] as $key => $value) {
            if ($_FILES["file"]["name"][$key] != "") {
                if ($value == UPLOAD_ERR_OK) {
                    $origfilename = $_FILES["file"]["name"][$key];
                    $filename = explode(".", $_FILES["file"]["name"][$key]);
                    $filenameext = $filename[count($filename) - 1];
                    unset($filename[count($filename) - 1]);
                    $filename = implode(".", $filename);
                    $filename = substr($filename, 0, 15) . "." . $filenameext;
                    $file_ext_allow = FALSE;
                    for ($x = 0; $x < count($file_types_array); $x++) {
                        if ($filenameext == $file_types_array[$x]) {
                            $file_ext_allow = TRUE;
                        }
                    }
                    if ($file_ext_allow) {
                        if ($_FILES["file"]["size"][$key] < $max_file_size) {
                            if (move_uploaded_file($_FILES["file"]["tmp_name"][$key], $upload_dir . $filename)) {
                                $success_messages .= "<p>File uploaded successfully. - <a href='" . $upload_dir . $filename . "' target='_blank'>" . $filename . "</a></p>";
                            } else {
                                $error_messages .= '<p>'.$origfilename . " was not successfully uploaded</p>";
                            }
                        } else {
                            $error_messages .= '<p>'.$origfilename . " was too big, not uploaded</p>";
                        }
                    } else {
                        $error_messages .= '<p>'.$origfilename . " had an invalid file extension, not uploaded</p>";
                    }
                } else {
                    $error_messages .= '<p>'.$origfilename . " was not successfully uploaded</p>";
                }
            }
        }
    }
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'voucher_import') {
   		load_vouchers_to_db( $success_messages, $error_messages );
	}
}

function load_vouchers_to_db( $success_messages, $error_messages ) {
    global $wpdb;

    $fieldseparator = ",";
    $lineseparator = "\n";
    $csvfile = "../wp-content/uploads/espresso/vouchers.csv";

    function getCSVValues($string, $separator = ",") {
        global $wpdb;
        $wpdb->show_errors();
        $elements = explode($separator, $string);
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');

            if ($nquotes % 2 == 1) {
                for ($j = $i + 1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j - $i + 1, implode($separator, array_slice($elements, $i, $j - $i + 1)));
                        break;
                    }
                }
            }

            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr = & $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
        }

        return $elements;
    }

    if (!file_exists($csvfile)) {
  		$error_messages .= '<p>File not found. Make sure you specified the correct path.</p>';
 	 	espresso_display_voucher_import_messages( $success_messages, $error_messages );
       exit;
    }

    $file = fopen($csvfile, "r");

    if (!$file) {
  		$error_messages .= '<p>Error opening data file.</p>';
  	 	espresso_display_voucher_import_messages( $success_messages, $error_messages );
       exit;
    }

    $size = filesize($csvfile);

    if (!$size) {
  		$error_messages .= '<p>File is empty.</p>';
  	 	espresso_display_voucher_import_messages( $success_messages, $error_messages );
      exit;
    }

    $file = file_get_contents($csvfile);
    $dataStrings = explode("\r", $file);

    $i = 0;
 	$tot_records = 0;
   
 
/*echo '<pre style="height:auto;border:2px solid lightblue;">' . print_r( $dataStrings, TRUE ) . '</pre><br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>';die();*/

    foreach ($dataStrings as $data) {
        ++$i;

        for ($j = 0; $j < $i; ++$j) {
            $strings = getCSVValues($dataStrings[$j]);
        }
		
		//echo "<pre>".print_r($strings,true)."</pre>";
		//echo '<h4>$valid : ' . $valid . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
       
		if (array_key_exists('0', $strings)) {
          //  echo "The  element is in the array<br />";
            $skip = $strings[0];
		
			if ($skip >= "1") {
               				                
				//Add voucher data               
				if ( $wpdb->insert( 
					EVENTS_GROUPON_CODES_TABLE, 
					array( 
						'groupon_code' => $strings[1], 
						'groupon_holder' => $strings[2],
					), 
					array( 
						'%s', // groupon_code
						'%s',  // groupon_holder
					) 
				) === false ) {
					print $wpdb->print_error();
				}
			  $tot_records++;
			}
        }
    }

    unlink($csvfile);
    if (!file_exists($csvfile)) {
        $success_messages .= '<p>Temporary upload file has been successfully deleted.</p>';
    }
 	$success_messages .='<p>Added a total of '.$tot_records.' vouchers to the database.</p>';
 	espresso_display_voucher_import_messages( $success_messages, $error_messages );

}

function espresso_display_voucher_import_messages( $success_messages = '', $error_messages = '' ) {
	if ($success_messages != '') {
		//showMessage( $success_messages );
		echo '<div id="message1" class="updated fade"><p>' . $success_messages . '</p></div>';
	}

	if ($error_messages != '') {
		//showMessage( $error_messages, TRUE );
		echo '<div id="message2" class="error fade fade-away"><p>' . $error_messages . '</p></div>';
	}	
}