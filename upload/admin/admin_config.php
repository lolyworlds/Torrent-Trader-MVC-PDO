<?php
#======================================================================#
# Configuration Panel by djhowarth 
#======================================================================#
 if ($action == "settings")
 {          
     if ($do == 'save')
     {                             
         #$file = new SplFileObject('backend/config.php', 'w');                                
         #$file->fwrite('<?php ' . "\r\n\r\n" . '$site_config = ' . var_export((array)$site_config, true) . ';');
         write_log( '<pre>', print_r($_POST, true), '</pre>' );
         die;
     }                               
     
     stdhead("Site Configuration");
     navmenu();
     
     begin_frame("Site Configuration - Incompleted!");
     ?>
     
     <!-- CSS to be moved... -->
     <style type="text/css">
     #sortable-list
     {
         padding: 0;
     }
     li.sortme
     {
         padding: 4px 8px; 
         color: #000; 
         cursor: move; 
         list-style: none; 
         width: 100px; 
         background: #ddd; 
         margin: 10px 0; 
         border: 1px solid #999;
     }
     #message-box
     {
         background: #fffea1; 
         border: 2px solid #fc0; 
         padding:4px 8px; 
         margin: 0 0 14px 0; 
         width: 500px; 
     }
     </style>
     
     <!-- JS to be moved... -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.js"></script>
    <script type="text/javascript">
/* when the DOM is ready */
jQuery(document).ready(function() {
  /* grab important elements */
  var sortInput = jQuery('#sort_order');
  var submit = jQuery('#autoSubmit');
  var messageBox = jQuery('#message-box');
  var list = jQuery('#sortable-list');
  /* create requesting function to avoid duplicate code */
  var request = function() {
    jQuery.ajax({
      beforeSend: function() {
        messageBox.text('Updating the sort order in the database.');
      },
      complete: function() {
        messageBox.text('Database has been updated.');
      },
      data: 'sort_order=' + sortInput[0].value + '&ajax=' + submit[0].checked + '&do_submit=1&byajax=1', //need [0]?
      type: 'post',
      url: '<?php echo $_SERVER["REQUEST_URI"]; ?>'
    });
  };
  /* worker function */
  var fnSubmit = function(save) {
    var sortOrder = [];
    list.children('li').thisEach(function(){
      sortOrder.push(jQuery(this).data('id'));
    });
    sortInput.val(sortOrder.join(','));
    console.log(sortInput.val());
    if(save) {
      request();
    }
  };
  /* store values */
  list.children('li').thisEach(function() {
    var li = jQuery(this);
    li.data('id',li.attr('title')).attr('title','');
  });
  /* sortables */
  list.sortable({
    opacity: 0.7,
    update: function() {
      fnSubmit(submit[0].checked);
    }
  });
  list.disableSelection();
  /* ajax form submission */
  jQuery('#dd-form').bind('submit',function(e) {
    if(e) e.preventDefault();
    fnSubmit(true);
  });
});
     </script>

     <form id="dd-form" method="post" action="admincp.php?action=settings&amp;do=save">
     <input type="hidden" name="sort_order" id="sort_order" value="<?php echo $site_config['torrenttable_columns']; ?>" />
     <table border="0" width="100%" cellpadding="3" cellspacing="3">
 
     <!-- File Path(s) -->
     <tr>
          <td colspan="2"><b>File Storage Paths:</b><br />&#9492; <small>Must be CHMOD 755 and absolute paths.</small></td>
     </tr>
     <tr>
          <td>Path to directory where .torrents will be stored:</td>
          <td><input type="text" name="site_config[torrent_dir]" value="<?php echo $site_config['torrent_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where .nfo's will be stored:</td>
          <td><input type="text" name="site_config[nfo_dir]" value="<?php echo $site_config['nfo_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where blocks's will be stored:</td>
          <td><input type="text" name="site_config[blocks_dir]" value="<?php echo $site_config['blocks_dir']; ?>" size="50" /></td>
     </tr>
     <tr>
          <td>Path to directory where <i>Disk</i> cache will be stored:</td>
          <td><input type="text" name="site_config[cache_dir]" value="<?php echo $site_config['cache_dir']; ?>" size="50" /></td>
     </tr>
     <!-- File Path(s) -->
     
     <!-- Tracker Options -->
     <tr>
         <td colspan="2"><b>Tracker Settings:</b><br />&#9492; <small>Main settings and options.</small></td> 
     </tr>
     <tr>
         <td>Site Name:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITENAME"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Site Email:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITEEMAIL"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Site URL:</td>
         <td><input type="text" name="" value="<?php echo $site_config["SITEURL"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Default Theme:</td>
         <td>
         <select name="site_config[default_theme]">
         <?php $res = SQL_Query_exec("SELECT * FROM `stylesheets`");
               while ($row = mysqli_fetch_assoc($res)): ?>
         <option value="<?php echo $row["id"]; ?>" <?php echo ( $row["id"] == $site_config["default_theme"] ? 'selected="selected"' : null ); ?>><?php echo $row["name"]; ?></option>
         <?php endwhile; ?>
         </select>
         </td>
     </tr>
     <tr>
         <td>Default Language:</td>
         <td>
         <select name="site_config[default_language]">
         <?php $res = SQL_Query_exec("SELECT * FROM `languages`");
               while ($row = mysqli_fetch_assoc($res)): ?>
         <option value="<?php echo $row["id"]; ?>" <?php echo ( $row["id"] == $site_config["default_language"] ? 'selected="selected"' : null ); ?>><?php echo $row["name"]; ?></option>
         <?php endwhile; ?>
         </select>
         </td> 
     </tr>
     <tr>
         <td>Site Charset:</td>
         <td><input type="text" name="" value="<?php echo $site_config["CHARSET"]; ?>" size="8" /></td>
     </tr>
     <tr>
         <td>Announce Url:</td>
         <td><input type="text" name="" value="<?php echo $site_config["announce_list"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Passkey URL:</td>
         <td><input type="text" name="" value="<?php echo $site_config["PASSKEYURL"]; ?>" size="50" /></td>
     </tr>
     <!-- Tracker Options -->
   
     <!-- Image Uploads -->
     <tr>
         <td colspan="2"><b>Image Uploads:</b><br />&#9492; <small>Manage image uploads.</small></td>
     </tr>
     <tr>
         <td>Max File Size:</td>
         <td><input type="text" name="" value="<?php echo $site_config["image_max_filesize"]; ?>" size="5" /> kb</td>
     </tr>
     <tr>
         <td>File Types:</td>
         <td>
         <input type="checkbox" name="site_config[allowed_image_types][image/png]" value=".png" <?php echo ( isset($site_config['allowed_image_types']['image/png']) ? 'checked="checked"' : null ); ?> /> image/png
         <input type="checkbox" name="site_config[allowed_image_types][image/gif]" value=".gif" <?php echo ( isset($site_config['allowed_image_types']['image/gif']) ? 'checked="checked"' : null ); ?> /> image/gif
         <input type="checkbox" name="site_config[allowed_image_types][image/jpg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/jpg']) ? 'checked="checked"' : null ); ?> /> image/jpg  
         <input type="checkbox" name="site_config[allowed_image_types][image/jpeg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/jpeg']) ? 'checked="checked"' : null ); ?> /> image/jpeg 
         <input type="checkbox" name="site_config[allowed_image_types][image/pjpeg]" value=".jpg" <?php echo ( isset($site_config['allowed_image_types']['image/pjpeg']) ? 'checked="checked"' : null ); ?>/> image/pjpeg        
         </td>
     </tr>
     <!-- Image Uploads -->
     
     <!-- Wait Times -->
     <tr>
         <td colspan="2"><b>Wait Times:</b><br />&#9492; <small>Configure wait times.</small></td>
     </tr>
     <tr>
         <td>Enable Wait Times:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config['MEMBERSONLY_WAIT'] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config['MEMBERSONLY_WAIT'] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Usergroups Wait:</td>
         <td><input type="text" name="" value="<?php echo $site_config["WAIT_CLASS"]; ?>" size="50" /></td> 
     </tr>
     <tr>
         <td>Times (A):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSA"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOA"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITA"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (B):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSB"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOB"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITB"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (C):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSC"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOC"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITC"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Times (D):</td>
         <td><input type="text" value="<?php echo $site_config["GIGSD"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["RATIOD"]; ?>" size="3" /> <input type="text" value="<?php echo $site_config["WAITD"]; ?>" size="3" /></td>
     </tr>
     <!-- Wait Times -->
     
     <!-- Mail Settings -->
     <tr>
         <td colspan="2"><b>Mail Settings:</b><br />&#9492; <small>Configure outgoing mail.</small></td> 
     </tr>
     <tr>
         <td>Mail Type:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_type"] == "php" ? 'checked="checked"' : null ); ?> /> PHP <input type="radio" name="" value="" <?php echo ( $site_config["mail_type"] == "pear" ? 'checked="checked"' : null ); ?> /> Pear</td>
     </tr>
     <tr>
         <td>SMTP Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_host"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP User:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_user"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_pass"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>SMTP Port:</td>
         <td><input type="text" name="" value="<?php echo $site_config["mail_smtp_port"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>SMTP Auth:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_smtp_auth"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["mail_smtp_auth"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>SMTP SSL:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["mail_smtp_ssl"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["mail_smtp_ssl"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <!-- Mail Settings -->
     
     <!-- Cache Settings -->
     <tr>
         <td colspan="2"><b>Cache Settings:</b><br />&#9492; <small>Configure cache.</small></td> 
     </tr>            
     <tr>
         <td>Cache Type:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "disk" ? 'checked="checked"' : null ); ?> /> Disk <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "apc" ? 'checked="checked"' : null ); ?> /> APC <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "memcache" ? 'checked="checked"' : null ); ?> /> Memcache <input type="radio" name="" value="" <?php echo ( $site_config["cache_type"] == "xcache" ? 'checked="checked"' : null ); ?> /> XCache</td>
     </tr>
     <tr>
         <td>Memcache Host:</td>
         <td><input type="text" name="" value="<?php echo $site_config["cache_memcache_host"]; ?>" size="50" /></td>
     </tr>
     <tr>
         <td>Memcache Port:</td>
         <td><input type="text" name="" value="<?php echo $site_config["cache_memcache_port"]; ?>" size="50" /></td>
     </tr>
     <!-- Cache Settings -->
     
     <!-- Ratio Warnings -->
     <tr>
         <td colspan="2"><b>Ratio Warnings:</b><br />&#9492; <small>Configure ratio warnings.</small></td>
     </tr>
     <tr>
         <td>Enable:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ratiowarn_enable"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["ratiowarn_enable"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Minimum Ratio:</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_minratio"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Minimum Downloaded (GB):</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_mingigs"]; ?>" size="3" /></td>
     </tr>
     <tr>
         <td>Days to Warning:</td>
         <td><input type="text" name="" value="<?php echo $site_config["ratiowarn_daystowarn"]; ?>" size="3" /></td>
     </tr>
     <!-- Ratio Warnings -->
     
     <!-- Blocks Navigation -->
     <tr>
         <td colspan="2"><b>Blocks Management:</b><br />&#9492; <small>Configure Blocks settings.</small></td>
     </tr>
     <tr>
         <td>Left Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["LEFTNAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["LEFTNAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Right Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["RIGHTNAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["RIGHTNAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Middle Nav:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["MIDDLENAV"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( !$site_config["MIDDLENAV"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <!-- Blocks Navigation -->
     
     <!-- Cleanup / Announce Settings -->
     <tr>
         <td colspan="2"><b>Cleanup &amp; Announce:</b><br /></td>
     </tr>
     <tr>
         <td>Peer Limit:</td>
         <td><input type="text" name="" value="<?php echo $site_config["PEERLIMIT"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Autoclean Interval:</td>
         <td><input type="text" name="" value="<?php echo $site_config["autoclean_interval"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Announce Interval:</td>
         <td><input type="text" name="" value="<?php echo $site_config["announce_interval"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Site Log Cleanup:</td>
         <td><input type="text" name="" value="<?php echo $site_config["LOGCLEAN"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Signup Timeout</td>
         <td><input type="text" name="" value="<?php echo $site_config["signup_timeout"]; ?>" size="4" /></td>
     </tr>
     <tr>
         <td>Dead Torrents:</td>
         <td><input type="text" name="" value="<?php echo $site_config["max_dead_torrent_time"]; ?>" size="4" /></td>
     </tr>
     <!-- Cleanup / Announce Settings -->
     
     <!-- Torrents Settings -->
     <tr>
         <td colspan="2"><b>Torrents Settings:</b><br />&#9492; <small>Configure Torrent settings.</small></td>
     </tr>                 
     <tr>
         <td>Allow External Torrents:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ALLOWEXTERNAL"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["ALLOWEXTERNAL"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Uploaders Only:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["UPLOADERSONLY"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["UPLOADERSONLY"] ? 'checked="checked"' : null ); ?> /> No</td>
     </tr>
     <tr>
         <td>Allow Anonymous Upload:</td>
         <td><input type="radio" name="" value="" <?php echo ( $site_config["ANONYMOUSUPLOAD"] ? 'checked="checked"' : null ); ?> /> Yes <input type="radio" name="" value="" <?php echo ( ! $site_config["ANONYMOUSUPLOAD"] ? 'checked="checked"' : null ); ?> / > No</td>
     </tr>
     <tr>
         <td valign="top">Upload Rules:</td>
         <td><textarea name="" cols="39" rows="4"><?php echo $site_config["UPLOADRULES"]; ?></textarea></td>
     </tr>
     <!-- Torrents Settings -->
     
     <!-- TorrentTable -->
     <tr>
         <td colspan="2"><b>TorrentTable:</b><br />&#9492; <small>Configure TorrentTable.</small></td>
     </tr>
     <tr>
         <td valign="top">TorrentTable Columns:</td><!-- Needs finishing... -->
         <td>
         <?php $column = array('category', 'name', 'dl', 'uploader', 'comments', 'completed', 'size', 'seeders', 'leechers', 'health', 'ratio', 'external', 'wait', 'rating', 'added', 'nfo'); ?>
         <ul id="sortable-list">
           <?php for ($i = 0; $i < count($column); $i++): ?>
           <li class="sortme" title="<?php echo $column[$i]; ?>"><input type="checkbox" name="" value="" <?php echo ( in_array( $column[$i], explode(',', $site_config['torrenttable_columns']) ) ? 'checked="checked"' : null ); ?> /> <?php echo $column[$i]; ?></li>
           <?php endfor; ?>
         </ul>
         </td>
     </tr>
     <tr>
         <td>TorrentTable Expand:</td>
         <td>
         <?php $expand = array(''); ?>
         </td>
     </tr>
     <!-- TorrentTable -->
     
     <tr>
         <td colspan="2" align="right">
         <input type="reset" value="Reset" />
         <input type="submit" value="Update" />
         </td>
     </tr>
     </table>
     </form>

     <?php 
     end_frame();
     stdfoot();
 }