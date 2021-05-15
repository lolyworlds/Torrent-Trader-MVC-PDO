<?php
  class Torrent {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }


        // Get All User Array
        public function getAll($id){
                $row = DB::run(" SELECT torrents.anon, torrents.seeders, torrents.tube, torrents.banned, 
            torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.torrentlang, torrents.category,
            torrents.last_action, torrents.numratings, torrents.name, torrents.imdb, 
            torrents.owner, torrents.save_as, torrents.descr, torrents.visible, 
            torrents.size, torrents.added, torrents.views, torrents.hits, 
            torrents.times_completed, torrents.id, torrents.type, torrents.external, 
            torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, 
            torrents.freeleech, torrents.vip, 
            IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) 
            AS rating, torrents.numratings, categories.name 
            AS cat_name, torrentlang.name 
            AS lang_name, torrentlang.image 
            AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy 
            FROM torrents 
            LEFT JOIN categories 
            ON torrents.category = categories.id 
            LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
            LEFT JOIN users ON torrents.owner = users.id 
            WHERE torrents.id = $id");
                $user1 = $row->fetchAll(PDO::FETCH_ASSOC);
            return $user1;
         }

public function getTorrentWhere ($where, $parent_check) 
{
 $stmt =   $this->db->run("
    SELECT COUNT(*) 
    FROM torrents 
    $where $parent_check
    ")->fetchColumn();

   return $stmt;
}

public function getCatById () 
{
    $row = $this->db->run("
    SELECT id 
    FROM categories");

    return $row;
}


public function getTorrentByCat ($where, $parent_check, $orderby, $limit) 
{
    $row = $this->db->run("
	SELECT 
	torrents.id, torrents.anon, torrents.announce, torrents.tube,  torrents.imdb, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, 
	torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name 
	AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, 
	IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) 
	AS rating FROM torrents 
	LEFT JOIN categories
	ON category = categories.id 
	LEFT JOIN users 
	ON torrents.owner = users.id 
	$where $parent_check $orderby $limit");
	return $row;
}


public function getCatByParent () 
{
    $row = $this->db->run("
    SELECT distinct parent_cat 
    FROM categories 
    ORDER BY parent_cat");
	return $row;
}

public function getCatByParentName () 
{
    $row = $this->db->run("
    SELECT * FROM categories ORDER BY parent_cat, name");
	return $row;
}

public function getSubCatByParentName ($parent_cat) 
{
    $row = $this->db->run("
	SELECT id, name, parent_cat 
	FROM categories 
	WHERE parent_cat='$parent_cat' 
	ORDER BY name");
	return $row;
}

public function getCatSort()
{
    $row = $this->db->run("SELECT id, name FROM categories ORDER BY sort_index");

	return $row;
}

public function getCatSortAll($where, $date_time, $orderby, $limit)
{
    $row = $this->db->run("
    SELECT 
    torrents.id, torrents.anon, torrents.category, torrents.sticky, torrents.imdb, torrents.tube, torrents.tube, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, 
    torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name 
    AS cat_name, categories.parent_cat 
    AS cat_parent, categories.image 
    AS cat_pic, users.username, users.privacy 
    FROM torrents 
    LEFT JOIN categories 
    ON category = categories.id 
    LEFT JOIN users 
    ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby $limit
    ");

	return $row;
}


public function getCatwhere($where)
{
    $row = $this->db->run("
    SELECT COUNT(*) 
    FROM torrents 
    LEFT JOIN categories 
    ON category = categories.id 
    $where
    ")->fetchColumn();

   return $row;
   }
  }