<?php
class Client extends Controller
{

    public function __construct()
    {
        // $this->userModel = $this->model('User');
    }

    public function index()
    {
        dbconn();
        global $config;
        if ($_SESSION["class"] < 6) {
            show_error_msg("Error", "Access denied.");
        }

        if (isset($_POST['ban'])) {
            DB::run("INSERT INTO agents (agent_name, hits, ins_date) VALUES (?,?,?)", [$_POST['ban'], 1, get_date_time()]);
        }
        $res11 = DB::run("SELECT client, peer_id FROM peers GROUP BY client");
        stdhead("Torrent clients");

        begin_frame("All Clients");?>
       <center><b>Current Clients Connected</b></center>
       <form id="ban" method="post" action="<?php echo TTURL; ?>/client">
       <table class='table table-striped table-bordered table-hover'><thead>
       <tr><th class=table_head>Client</th>
       <th class=table_head>Peer ID</th>
       <th class=table_head>To ban use</th>
       <th class=table_head>Is Banned</th>
       </th></tr></thead><tbody></tbody>

        <?php while ($arr12 = $res11->fetch(PDO::FETCH_ASSOC)) {
            $peer = $arr12['peer_id'];
            $peer = substr($peer, 0, 8);
            $peer2 = $peer;

            $arr3 = DB::run("SELECT hits FROM agents WHERE agent_name=?", [$peer2])->fetch();
            $isbanned = "<font color='green'><b>Yes</b></font>";
            if ($arr3 == 0) {
                $isbanned = "<font color='red'><b>No</b></font>";
            }
            ?>
        <tr>
        <td class=table_col1>&nbsp; <?php echo $arr12['client']; ?> &nbsp;</td>
        <td class=table_col2>&nbsp; <?php echo $arr12['peer_id']; ?> &nbsp;</td>
        <td class=table_col2>&nbsp; <?php echo $peer2; ?> &nbsp;</td>
        <td class=table_col2>&nbsp; <?php echo $isbanned; ?> &nbsp;</td></tr>
        <?php }?>
        </tbody></table>


        <table border="0" cellpadding="3" align="center">
	    	<tr><td align="center"><b>Enter Ban Code :</b> <input type="text" size="30" name="ban" />&nbsp;<button type='submit' class='btn btn-sm btn-primary'>Ban</button></td></tr>
        <tr><td align="center"><a href='<?php echo TTURL; ?>/client/banned'><button type="button" class="btn btn-sm btn-success">View Banned</button></a></td></tr>
        </tr>
        </table>
        </form>
        <?php
        end_frame();
        stdfoot();
    }

    public function banned()
    {
        dbconn();
        global $config;
        if ($_SESSION["class"] < 6) {
            show_error_msg("Error", "Access denied.");
        }

        if (isset($_POST['unban'])) {
            foreach ($_POST['unban'] as $deleteid) {

                DB::run("DELETE FROM agents WHERE agent_id=?", [$deleteid]);
            }
        }

        $getallfromdb = DB::run("SELECT * FROM agents")->fetchAll(PDO::FETCH_ASSOC);
        stdhead("Torrent clients");

        begin_frame("All Clients");?>
        <center><b>Current Banned Clients</b></center>
        <form id="unban" method="post" action="<?php echo TTURL; ?>/client/banned">
        <input type="hidden" name="unban" value="unban" />
        <table class='table table-striped table-bordered table-hover'><thead>
        <tr><th class=table_head>Name</th>
        <th class=table_head>Banned</th>
        <th class=table_head>Added</th>
        <th class='table_head'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
        </th></tr></thead><tbody></tbody>
        <?php foreach ($getallfromdb as $arr14) {
            $isbanned = "<font color='green'><b>Yes</b></font>";
            if ($arr14['hits'] == 0) {
                $isbanned = "<font color='red'><b>No</b></font>";
            }
            ?>
        <tr>
        <td class=table_col1>&nbsp; <?php echo $arr14['agent_name']; ?> &nbsp;</td>
        <td class=table_col2>&nbsp; <?php echo $isbanned; ?> &nbsp;</td>
        <td class=table_col2>&nbsp; <?php echo $arr14['ins_date']; ?> &nbsp;</td>
        <td class=table_col1><input type='checkbox' name='unban[]' value='<?php echo $arr14['agent_id']; ?>' /></td></tr>
        <?php }?>
        </tbody></table>
        <center><a href='<?php echo TTURL; ?>/client'><button type="button" class="btn btn-sm btn-success">View Current Clients</button></a>&nbsp;
        &nbsp;
        <button type="input" class="btn btn-sm btn-danger">Unban</button></center>
        </form>
        <?php
        end_frame();
        stdfoot();
    }
}