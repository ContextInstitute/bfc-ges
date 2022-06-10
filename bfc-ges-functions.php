<?php

add_action( 'bp_email', 'bfc_email_topline', 10, 2);

function bfc_email_topline ($email_type, $bp_email) {
	global $topline;
	global $bp;
	$avatar = bp_core_fetch_avatar( array( 'item_id' => bp_loggedin_user_id(), 'type'   => 'thumb', 'width'  => '40', 'height' => '40', 'html' => false ));
	$name = bp_core_get_userlink(bp_loggedin_user_id());

	$tl_start = '<table role="presentation" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="vertical-align: middle; text-align: right;font-weight:normal;color:#7f868f;font-size:14px;">';
	$tl_avatar = '</td><td style="vertical-align: middle; padding-left: 12px;"><img src="' . $avatar . '" height="39" width="39" style="border-radius: 20%;max-width: 39px;vertical-align: middle;" />';
	$tl_end = '</td></tr></tbody></table>';
	$topline = '';

	switch ($email_type) {
		case "bp-ges-single":
			$topline =  $tl_start . 'A new forum post from<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "bp-ges-digest":
			$topline = '';
			break;
		case "group-message-email":
		case "messages-unread":
			$topline =  $tl_start . 'You have a new message from<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "bbp-new-forum-topic":
			$topline =  $tl_start . 'A new forum thread by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "bbp-new-forum-reply":
			$topline =  $tl_start . 'A new forum reply from<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "activity-comment":
			$topline =  $tl_start . 'A new comment by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "activity-comment-author":
			$topline =  $tl_start . 'A new reply by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "activity-at-message":
		case "groups-at-message":
			$topline = $tl_start . "You've been mentioned by<br>" . $name . $tl_avatar . $tl_end;
			break;
		case "new-mention":
		case "new-mention-group":
			$topline =  $tl_start . 'You have a new mention by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "groups-details-updated":
			$topline =  $tl_start . 'Group details updated by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "groups-invitation":
			$topline =  $tl_start . 'From ' . $name . $tl_avatar . $tl_end;
			break;
		case "groups-member-promoted":
			$topline = 'Congratulations!';
			break;
		case "groups-membership-request":
			$topline =  $tl_start . 'A new member request from<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "groups-membership-request-accepted":
		case "groups-membership-request-rejected":
			$topline =  $tl_start . 'From the group steward<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "settings-verify-email-change":
			$topline =  $tl_start . 'For your security'. $tl_end;
			break;
			case "bp-ges-notice":
			$topline =  $tl_start . 'From your group steward<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "bp-ges-welcome":
			$topline = 'Welcome to the group!';
			break;
		case "settings-password-changed":
			$topline = 'Confirming your password change';
			break;
		case "invites-member-invite":
		case "invite-anyone-invitation":
			$topline =  $tl_start . "You've been invited to join " . bp_get_option( 'blogname' ) . 'by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "content-moderation-email":
		case "user-moderation-email":
			$topline = ' ';
			break;
		case "zoom-scheduled-meeting-email":
			$topline =  $tl_start . 'Zoom meeting scheduled by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "zoom-scheduled-webinar-email":
			$topline =  $tl_start . 'Zoom webinar scheduled by<br>' . $name . $tl_avatar . $tl_end;
			break;
		case "core-user-registration":
		case "core-user-registration-with-blog":
			$topline = "You're registered!<br>Activate now to complete your access.";
			break;
		case "friends-request":
			$topline = $tl_start . $name . '<br>requests your friendship' . $tl_avatar . $tl_end;
			break;
		case "friends-request-accepted":
			$topline = $tl_start . $name . '<br>accepted your request' . $tl_avatar . $tl_end;
		default:
			break;
	}
}

add_filter( 'bp_email_set_tokens', 'bfc_set_tokens', 100, 3 );

function bfc_set_tokens ($formatted_tokens, $tokens, $bp_email) {

	$email_type = $bp_email->get('type');
	$um_start = '<div style="font-family: charter, Georgia, Cambria, \'Times New Roman\', Times, serif; color: #4e535a; font-size: 18px">';
	$um_end = '</div>';
	$to = $bp_email->get('to');
	$toBPER = $to[0];
	$wp_user = $toBPER->get_user();
	$recipient_id = $wp_user->ID;
	$tokens['email.prefs'] = bp_core_get_user_domain($recipient_id).'settings/notifications/';

	switch ($email_type) {
		case "bp-ges-single":
			$recent_posts = wp_get_recent_posts(array ('numberposts'=> '1', 'post_type' => array('topic','reply'))); 
			$the_post = $recent_posts[0];
			$post_type = $the_post['post_type'];
			$post_id = $the_post['ID'];
			$topic_id = ($post_type == 'reply') ? bbp_get_reply_topic_id ($post_id) : $post_id;
			$topic_name ='<a href="' . bbp_get_topic_permalink( $topic_id ) . '">'. bbp_get_topic_title( $topic_id ) . '</a>';
			$forum_id = bbp_get_topic_forum_id($topic_id);
			$forum_title = bfc_get_forum_title($forum_id) ;
			$forum_link = esc_url( bbp_get_forum_permalink( $forum_id )) ;
			$group_link = substr($forum_link, 0, strpos($forum_link, "forum/"));
			$group_name = '<a href="' . $group_link . '">' . $forum_title . '</a>';
			$tokens['ges.subject'] = bp_core_get_user_displayname( bp_loggedin_user_id() );
			if ($post_type == 'reply') {
				$tokens['ges.subject'] .= ' replied to "';
			} else {
				$tokens['ges.subject'] .= ' created "';
			}
			$tokens['ges.subject'] .= bbp_get_topic_title( $topic_id ). '" in ' . $forum_title;
			$intro_close = ($post_type == 'reply') ? 'new reply:' : 'new thread:';
			$tokens['intro'] = $group_name . ' > ' . $topic_name . ' > ' . $intro_close;
			$tokens['usermessage'] = $um_start . wp_kses_post($tokens ['usermessage']). $um_end;
			return $tokens;
		case "messages-unread":
			$tokens['usermessage'] = $um_start . wp_kses_post($tokens ['usermessage']). $um_end;
			return $tokens;
		case "bbp-new-forum-topic":
			$tokens['discussion.content'] = $um_start . wp_kses_post($tokens ['discussion.content']). $um_end;
			return $tokens;
		case "bbp-new-forum-reply":
			$tokens['reply.content'] = $um_start . wp_kses_post($tokens ['reply.content']). $um_end;
			return $tokens;
		case "activity-comment":
		case "activity-comment-author":
		case "activity-at-message":
		case "groups-at-message":
			$tokens['usermessage'] = $um_start . wp_kses_post($tokens ['usermessage']). $um_end;
			return $tokens;
		case "new-mention":
		case "new-mention-group":
			$tokens['mentioned.content'] = $um_start . wp_kses_post($tokens ['mentioned.content']). $um_end;
			return $tokens;
		case "group-message-email":
			$tokens['message'] = $um_start . wp_kses_post($tokens ['message']). $um_end;
			return $tokens;
		case "groups-details-updated":
			$tokens['changed_text'] = $um_start . wp_kses_post($tokens ['changed_text']). $um_end;
			return $tokens;	
		case "bp-ges-notice":
		case "bp-ges-welcome":
			$tokens['usermessage'] = $um_start . wp_kses_post($tokens ['usermessage']). $um_end;
			return $tokens;
		case "invite-anyone-invitation":
			$tokens['ia.content'] = $um_start . wp_kses_post($tokens ['ia.content']). $um_end;
			return $tokens;
		
		// case "friends-request":
		// case "friends-request-accepted":
		// case "groups-membership-request-rejected":
		// case "groups-membership-request-accepted":
		// case "groups-membership-request":
		// case "groups-member-promoted":
		// case "groups-invitation":
		// case "settings-verify-email-change":
		// case "invites-member-invite":
		// case "zoom-scheduled-meeting-email":
		// case "zoom-scheduled-webinar-email":
		// case "bp-ges-digest":
		// case "settings-password-changed":
		// case "content-moderation-email":
		// case "user-moderation-email":
		// case "core-user-registration":
		// case "core-user-registration-with-blog":
		default:
			return $tokens;
	}
}
?>
