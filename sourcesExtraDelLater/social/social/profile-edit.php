<?php	
	require_once('includes/class-query.php');
	require_once('includes/class-insert.php');
	
	$logged_user_id = 1;
	
	if ( !empty ( $_POST ) ) {
		$update = $insert->update_user($logged_user_id, $_POST);
	}
	
	$user = $query->load_user_object($logged_user_id);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Edit Profile</title>
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
		<h1>Edit Profile</h1>
		<div class="content">
			<form method="post">
				<p>
					<label class="labels" for="name">Full Name:</label>
					<input name="user_nicename" type="text" value="<?php echo $user->user_nicename; ?>" />
				</p>
				<p>
					<label class="labels" for="email">Email Address:</label>
					<input name="user_email" type="text" value="<?php echo $user->user_email; ?>" />
				</p>
				<p>
					<label class="labels" for="password">Password:</label>
					<input name="user_pass" type="password" value="<?php echo $user->user_pass; ?>" />
				</p>
				<p>
					<input type="submit" value="Submit" />
				</p>
			</form>
		</div>
	</body>
</html>