<?php
	require_once('includes/class-query.php');
	require_once('includes/class-insert.php');
	
	if ( !empty ( $_POST ) ) {
		if ( $_POST['type'] == 'add' ) {
			$add_friend = $insert->add_friend($_POST['user_id'], $_POST['friend_id']);
		}
		
		if ( $_POST['type'] == 'remove' ) {
			$remove_friend = $insert->remove_friend($_POST['user_id'], $_POST['friend_id']);
		}
	}
	
	$logged_user_id = 1;
	
	if ( !empty ( $_GET['uid'] ) ) {
		$user_id = $_GET['uid'];
		$user = $query->load_user_object($user_id);
		
		if ( $logged_user_id == $user_id ) {
			$mine = true;
		}
	} else {
		$user = $query->load_user_object($logged_user_id);
		$mine = true;
	}

	$friends = $query->get_friends($logged_user_id);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Home</title>
		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body>
		<div id="navigation">
			<ul>
				<li><a href="/social">Home</a></li>
				<li><a href="profile-view.php">View Profile</a></li>
				<li><a href="profile-edit.php">Edit Profile</a></li>
				<li><a href="friends-directory.php">Member Directory</a></li>
				<li><a href="friends-list.php">Friends List</a></li>
				<li><a href="feed-view.php">View Feed</a></li>
				<li><a href="feed-post.php">Post Status</a></li>
				<li><a href="messages-inbox.php">Inbox</a></li>
				<li><a href="messages-compose.php">Compose</a></li>
			</ul>
		</div>
		<h1>View Profile</h1>
		<div class="content">
			<p>Name: <?php echo $user->user_nicename; ?></p>
			<p>Email Address: <?php echo $user->user_email; ?></p>
			<?php if ( !$mine ) : ?>
				<?php if ( !in_array($user_id, $friends) ) : ?>
					<p>
						<form method="post">
							<input name="user_id" type="hidden" value="<?php echo $logged_user_id; ?>" />
							<input name="friend_id" type="hidden" value="<?php echo $user_id; ?>" />
							<input name="type" type="hidden" value="add" />
							<input type="submit" value="Add as Friend" />
						</form>
					</p>
				<?php else : ?>
					<p>
						<form method="post">
							<input name="user_id" type="hidden" value="<?php echo $logged_user_id; ?>" />
							<input name="friend_id" type="hidden" value="<?php echo $user_id; ?>" />
							<input name="type" type="hidden" value="remove" />
							<input type="submit" value="Remove Friend" />
						</form>
					</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</body>
</html>