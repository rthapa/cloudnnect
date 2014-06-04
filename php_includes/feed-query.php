<?php
	require_once('feed-db.php');

	if ( !class_exists('QUERY') ) {
		class QUERY {
			public function load_user_object($user_id) {
				global $db;
				
				$table = 'users';
				
				$query = "
								SELECT * FROM $table
								WHERE id = $user_id
							";
				
				$obj = $db->select($query);
				
				
				if ( !$obj ) {
					return "No user found";
					exit();
				}
				
				return $obj[0];
			}
			
			public function load_all_user_objects() {
				global $db;
				
				$table = 's_users';
				
				$query = "
								SELECT * FROM $table
							";
				
				$obj = $db->select($query);
				
				if ( !$obj ) {
					return "No user found";
					exit();
				}
				
				return $obj;
			}
			
			public function get_friends($user_id) {
			
				global $db;
				
				$table = 'follow';
				
				$query = "
								SELECT id, following_id FROM $table
								WHERE follower_id = '$user_id' AND accepted='1'
							";
				
				$friends = $db->select($query);
				
				//as no friends exit here?
				if(!$friends ){
				return 'no friends';
				exit();
				}
				
				foreach ( $friends as $friend ) {
					$friend_ids[] = $friend->following_id;
				}
				
				return $friend_ids;
			}
			
			public function get_status_objects($user_id) {
			
				global $db;
				
				$table = 'file';
				
				$friend_ids = $this->get_friends($user_id);
				
				if ( !empty ( $friend_ids ) ) {
				    //appending our own users id so that our post can be shown in feed aswell
					array_push($friend_ids, $user_id);
				} else {
					//if friend is empty or no friend
					$friend_ids = array($user_id);
				}
				
				$accepted_ids = implode(', ', $friend_ids);
				
				$query = "
								SELECT * FROM $table
								WHERE owner_id IN ($accepted_ids) AND type='a'
								ORDER BY uploaddate DESC
							";
				
				$status_objects = $db->select($query);
				
				//as no uploads do this??
				
				if(!$status_objects){
				exit();
				}
				
				return $status_objects;
			}
			
			public function get_message_objects($user_id) {
				global $db;
				
				$table = 's_messages';
				
				$query = "
								SELECT * FROM $table
								WHERE message_recipient_id = '$user_id'
							";
				
				$messages = $db->select($query);
				
				if(!$messages){
					exit();
				}
								
				return $messages;
			}
			
			public function do_user_directory() {
				$users = $this->load_all_user_objects();
				
				foreach ( $users as $user ) { ?>
					<div class="directory_item">
						<h3><a href="/social/profile-view.php?uid=<?php echo $user->ID; ?>"><?php echo $user->user_nicename; ?></a></h3>
						<p><?php echo $user->user_email; ?></p>
					</div>
				<?php
				}
			}
			
			public function do_friends_list($friends_array) {
				foreach ( $friends_array as $friend_id ) {
					$users[] = $this->load_user_object($friend_id);
				}
								
				foreach ( $users as $user ) { ?>
					<div class="directory_item">
						<h3><a href="/social/profile-view.php?uid=<?php echo $user->ID; ?>"><?php echo $user->user_nicename; ?></a></h3>
						<p><?php echo $user->user_email; ?></p>
					</div>
				<?php
				}
			}
			
			public function do_news_feed($user_id) {
			
			//total followers
				$sqlFollowerCount = "SELECT COUNT(id) FROM follow WHERE following_id='$user_id' AND accepted='1'";
				$queryFollowerCount = mysqli_query(mysqli_connect("localhost", "root", "", "social"), $sqlFollowerCount);
				$totalFollowerRow = mysqli_fetch_row($queryFollowerCount);
				$totalFollower = $totalFollowerRow[0];
				
			//total following
				$sqlFollowingCount = "SELECT COUNT(id) FROM follow WHERE follower_id='$user_id' AND accepted='1'";
				$queryFollowingCount = mysqli_query(mysqli_connect("localhost", "root", "", "social"), $sqlFollowingCount);
				$totalFollowingRow = mysqli_fetch_row($queryFollowingCount);
				$totalFollowing = $totalFollowingRow[0];
			
			//used this cos it was throwing errors with 0 followings in feed-db
			/*
			if($totalFollowing < 1){
				echo 'noo feed';
	
			}else{
			}
			**/
				$status_objects = $this->get_status_objects($user_id);
				
				foreach ( $status_objects as $status ) {?>
				<!--
					<div class="status_item">
						<?php //$user = $this->load_user_object($status->owner_id); ?>
						<h3><a href="user.php?u=<?php //echo $user->username; ?>"><?php //echo $user->username; ?></a></h3>
						<p><?php //echo $status->fileName; ?></p>
					</div>
				-->
				<!-- fetch users detail  -->
				<?php
				$user = $this->load_user_object($status->owner_id);
				$logged_user = $_SESSION['username'];
				//total likes 
				$sqlLikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$status->id' AND type='like'";
				$queryLikesCount = mysqli_query(mysqli_connect("localhost", "root", "", "social"), $sqlLikesCount);
				$totalLikesRow = mysqli_fetch_row($queryLikesCount);
				$totalLikes = $totalLikesRow[0];

				//total dislikes
				$sqlDislikesCount = "SELECT COUNT(id) FROM likedislike WHERE fileId='$status->id' AND type='dislike'";
				$queryDislikesCount = mysqli_query(mysqli_connect("localhost", "root", "", "social"), $sqlDislikesCount);
				$totalDislikesRow = mysqli_fetch_row($queryDislikesCount);
				$totalDislikes = $totalDislikesRow[0];
				
				//total downloads
				$totalDownloads = $status->totalDownloads;
				
				//uploader pic
				$avatar = $user->avatar;
				if($avatar==null){
					$uploader_pic_link = 'Images/avatardefault.jpg';
				}else{
					$uploader_pic_link = 'user/'.$status->owner.'/'.$avatar;
				}
				
				/*
				//like and dislike button according to the users likedislike table
				$dislikeButton = '<button title="I dislike this unchanged" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Dislike</button>';
				$likeButton = '<button title="I like this unchanged" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan\')" class="likeButton">Like</button>';

				$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$logged_user' AND fileId='$status->id' LIMIT 1";
				$queryLikeDislike = mysqli_query(mysqli_connect("localhost", "root","","social"), $sqlLikeDislike);
				while ($row = mysqli_fetch_array($queryLikeDislike, MYSQLI_ASSOC)) {
					$type = $row['type'];

					if($type == 'like'){
						$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Dislike</button>';
						$likeButton = '<button title="You liked this" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan\')" class="likeButton">Liked</button>';
					}else if($type == 'dislike'){
						$dislikeButton = '<button title="You disliked this" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan\')" class="likeButton" style="margin-left: 10px;">Disliked</button>';
						$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan\')" class="likeButton">Like</button>';
					}
				}
				*/
				//check for likes and dsilike in from the user in this particular file
				$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan'.$status->id.'\')" class="likeButton" id="idDislike'.$status->id.'" style="margin-left: 10px;">Dislike</button>';
				$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan'.$status->id.'\')" class="likeButton" id="idLike'.$status->id.'">Like</button>';

				$sqlLikeDislike = "SELECT * FROM likedislike WHERE username='$logged_user' AND fileId='$status->id' LIMIT 1";
				$queryLikeDislike = mysqli_query(mysqli_connect("localhost", "root", "", "social"), $sqlLikeDislike);
				while ($row = mysqli_fetch_array($queryLikeDislike, MYSQLI_ASSOC)) {
					$type = $row['type'];

					if($type == 'like'){
						$dislikeButton = '<button title="I dislike this" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan'.$status->id.'\')" class="likeButton" id="idDislike'.$status->id.'" style="margin-left: 10px;">Dislike</button>';
						$likeButton = '<button title="You liked this" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan'.$status->id.'\')" class="likeButton" id="idLike'.$status->id.'">Liked</button>';
					}else if($type == 'dislike'){
						$dislikeButton = '<button title="You disliked this" onclick="likeToggle(\'dislike\','.$status->id.',\'dislikeBtnSpan'.$status->id.'\')" class="likeButton" id="idDislike'.$status->id.'" style="margin-left: 10px;">Disliked</button>';
						$likeButton = '<button title="I like this" onclick="likeToggle(\'like\','.$status->id.',\'likeBtnSpan'.$status->id.'\')" class="likeButton" id="idLike'.$status->id.'">Like</button>';
					}
				}

				//file keywords
				$keywordListArray = explode(" ", $status->keywords);
				$keywordList ="";
				/*
				foreach($keywordListArray as $k){
			    $keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
				}
				*/
				if(!$status->keywords ==""){
					foreach($keywordListArray as $k){
					if($k != ""){
				    	$keywordList .= "<a href='search.php?search=$k'><h5>$k</h5></a>";
					}
					}
				}else{
					$keywordList = "";
				}

				//give the permission to delete to only owner of the file.
				if($status->owner == $logged_user){
					$optionArrowList ="
							<li onclick=\"deleteFile('".$status->id."')\">Delete</li>
							<li>Report</li>
					";
				}else{
					$optionArrowList ="<a href=''><li>Report</li></a>";
				}

				?>
				<!-- create the divs  -->
				<div id="status_<?php echo $status->id; ?>" class="upload_boxes feed_upload_boxes">

					<div class="uploaderPic uploaderPicFeed">
						<a href="user.php?u=<?php echo $status->owner ?>"><img src="<?php echo $uploader_pic_link;?>" alt="<?php echo $status->owner ?>"></a>
					</div>
					<div class="left_top_info">
						<h5>Uploaded by <a href="user.php?u=<?php echo $status->owner ?>"><span class="rankColorClass<?php echo $user->id;?>"><?php echo $status->owner ?></span></a></h5>
						<script type="text/javascript">
  				 			checkr('<?php echo $user->userRank; ?>', '<?php echo $user->id;?>', 0);
						</script>
					</div>
					<div class="right_top_info">
						<h5><?php echo $status->uploaddate; ?></h5>
						<h5 id="optionArrow"><img src="Images/optionArrow.png" width="10px" height="6px">
						<ul>
							<?php echo $optionArrowList; ?>
						</ul>
						</h5>
					</div>
					<div class="mid_info">
						<a href="download.php?f=<?php echo $status->url;?>"><h4><?php echo $status->fileName; ?></h4></a>
						<h5><?php echo $status->description; ?></h5>
						<div class="userKeywordDiv">
							<?php echo $keywordList; ?>
						</div>
					</div>
					<div class="bot_info">
						<div class="bot_info_left">
							<span id="likeBtnSpan<?php echo $status->id; ?>"><?php echo $likeButton; ?></span>
							<h5 class="counter"><?php echo $totalLikes; ?></h5>
							<span id="dislikeBtnSpan<?php echo $status->id; ?>"><?php echo $dislikeButton ?></span>
							<h5 class="counter"><?php echo $totalDislikes; ?></h5>
						</div>
						
						<div class="bot_info_right">
							<h5 class="counterDownloads"><?php echo $totalDownloads; ?></h5>
							<a href="download.php?f=<?php echo $status->url;?>"><img src="Images/downloadLogo.png" title="Download"></a>
						</div>
					</div>
					
				</div>
				

				<?php
				}
				
			}
			
			public function do_inbox($user_id) {
				$message_objects = $this->get_message_objects($user_id);
				
				foreach ( $message_objects as $message ) {?>
					<div class="status_item">
						<?php $user = $this->load_user_object($message->message_sender_id); ?>
						<h3>From: <a href="/social/profile-view.php?uid=<?php echo $user->ID; ?>"><?php echo $user->user_nicename; ?></a></h3>
						<p><?php echo $message->message_subject; ?></p>
						<p><?php echo $message->message_content; ?></p>
					</div>
				<?php
				}
			}
		}
	}
	
	//$query = new QUERY;
?>