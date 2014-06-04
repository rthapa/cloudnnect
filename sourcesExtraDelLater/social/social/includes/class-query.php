<?php
	require_once('class-db.php');

	if ( !class_exists('QUERY') ) {
		class QUERY {
			public function load_user_object($user_id) {
				global $db;
				
				$table = 's_users';
				
				$query = "
								SELECT * FROM $table
								WHERE ID = $user_id
							";
				
				$obj = $db->select($query);
				
				if ( !$obj ) {
					return "No user found";
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
				}
				
				return $obj;
			}
			
			public function get_friends($user_id) {
				global $db;
				
				$table = 's_friends';
				
				$query = "
								SELECT ID, friend_id FROM $table
								WHERE user_id = '$user_id'
							";
				
				$friends = $db->select($query);
				
				foreach ( $friends as $friend ) {
					$friend_ids[] = $friend->friend_id;
				}
				
				return $friend_ids;
			}
			
			public function get_status_objects($user_id) {
				global $db;
				
				$table = 's_status';
				
				$friend_ids = $this->get_friends($user_id);
				
				if ( !empty ( $friend_ids ) ) {
					array_push($friend_ids, $user_id);
				} else {
					$friend_ids = array($user_id);
				}
				
				$accepted_ids = implode(', ', $friend_ids);
				
				$query = "
								SELECT * FROM $table
								WHERE user_id IN ($accepted_ids)
								ORDER BY status_time DESC
							";
				
				$status_objects = $db->select($query);
				
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
				$status_objects = $this->get_status_objects($user_id);
				
				foreach ( $status_objects as $status ) {?>
					<div class="status_item">
						<?php $user = $this->load_user_object($status->user_id); ?>
						<h3><a href="/social/profile-view.php?uid=<?php echo $user->ID; ?>"><?php echo $user->user_nicename; ?></a></h3>
						<p><?php echo $status->status_content; ?></p>
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
	
	$query = new QUERY;
?>