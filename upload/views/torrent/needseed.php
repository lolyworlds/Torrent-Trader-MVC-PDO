<?php
echo T_("TORRENT_NEED_SEED_MSG");
                        
                        ?>
                   
                        <div class='table-responsive'><table class='table table-striped'>
                        <thead><tr>
                            <th><?php echo T_("TORRENT_NAME"); ?></th>
                            <th><?php echo T_("UPLOADER"); ?></th>
                            <th><?php echo T_("LOCAL_EXTERNAL"); ?></th>
                            <th><?php echo T_("SIZE"); ?></th>
                            <th><?php echo T_("SEEDS"); ?></th>
                            <th><?php echo T_("LEECHERS"); ?></th>
                            <th><?php echo T_("COMPLETE"); ?></th>
                            <th><?php echo T_("ADDED"); ?></th>
                        </tr></thead>
                        
                        <?php 
                        
                        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                           
                           $type = ($row["external"] == "yes") ? T_("EXTERNAL") : T_("LOCAL"); 
                   
                           if ($row["anon"] == "yes" && ($CURUSER["edit_torrents"] == "no" || $CURUSER["id"] != $row["owner"]))
                               $owner = T_("ANONYMOUS");
                           elseif ($row["username"])
                               $owner = "<a href='$site_config[SITEURL]/users/profile?id=".$row["owner"]."'>" . class_user_colour($row["username"]) . "</a>";
                           else
                               $owner = T_("UNKNOWN_USER");
                   
                           ?>
                           
                           <tbody><tr>
                              <td><a href="<?php echo $site_config['SITEURL'] ?>/torrents/read?id=<?php echo $row["id"]; ?>"><?php echo CutName(htmlspecialchars($row["name"]), 40) ?></a></td>
                              <td><?php echo $owner; ?></td>
                              <td><?php echo $type; ?></td>
                              <td><?php echo mksize($row["size"]); ?></td>
                              <td><?php echo number_format($row["seeders"]); ?></td>
                              <td><?php echo number_format($row["leechers"]); ?></td>
                              <td><?php echo number_format($row["times_completed"]); ?></td>
                              <td><?php echo utc_to_tz($row["added"]); ?></td>
                           </tr></tbody>
                           
                        <?php
                        
                        }
                        
                        ?>
                        
                        </table></div>