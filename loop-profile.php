<?php
/**
 * Princeton 2006 template for displaying the standard Loop
 *
 * @package WordPress
 * @subpackage Princeton 2006
 * @since Princeton 2006 1.0
 */
?>

<?php

// utility function to parse csv file
function csvToArray($file, $delimiter) {
	if (($handle = fopen($file, 'r')) !== FALSE) {
		$i = 0;
		while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
			for ($j = 0; $j < count($lineArray); $j++) {
				$arr[$i][$j] = $lineArray[$j];
			}
			$i++;
		}
		fclose($handle);
	}
	return $arr;
}


$current_user = wp_get_current_user();
$curr_id = $current_user->ID;
$logged_in = $curr_id > 0;

if($logged_in):
	$first_name = $current_user->first_name;
	$nick_name = $current_user->nickname;
	$last_name = $current_user->last_name;
	$last_name_end = end(explode(' ', $last_name));

	$name_string = $first_name . ' ' . $last_name;
	if($nick_name && !empty($nick_name)){
		$name_string .= " (" . $nick_name . ")";
	}

	$email = $current_user->user_email;
	$email_lc = $email && !empty($email) ? strtolower($email) : "";

	$alt_last_name = get_user_meta($curr_id, 'alt_last_name');
	$alt_last_name = $alt_last_name && !empty($alt_last_name) ? $alt_last_name[0] : "";
	$alt_last_name_end = end(explode(' ', $alt_last_name));

	// form input variables
	$confirm_name_response = $_POST['confirm_name'];
	$confirm_name_response = $confirm_name_response && !empty($confirm_name_response) ? stripslashes($confirm_name_response) : false;
	$reset_match = false;

	if($confirm_name_response && $confirm_name_response == 'change_identity'){
		update_user_meta( $curr_id, 'housing_preferred_name', false);
		$reset_match = true;
	}

	// matching variables
	$surname_matches = array();
	$narrow_matches = array();

	$meta_preferred_name = get_user_meta($curr_id, 'housing_preferred_name'); // if the user has previously confirmed their identity
	$confirmed_user_name = $meta_preferred_name && !empty($meta_preferred_name) ? $meta_preferred_name[0] : false;
	$confirmed_user = false; // if the user has actively confirmed their identity
	$unique_match = false; // if the user is matched based on a unique field like ID
	$probable_match = false; // if narrowed down to one likely match
	$likely_matches = false; // if narrowed down to one likely match
	$possible_matches = false; // if narrowed down but still multiple matches
	$no_matches = false;
	$widen_net = false;
	$rejected_all = false;
	$no_text = "Nope, I don't see my name :(";

	if($confirm_name_response && !$confirmed_user_name){
		if($confirm_name_response == 'try_possible_matches'){
			$widen_net = true;
			$no_text = "Nope, still don't see it.";
		} else if($confirm_name_response == 'no_possible_matches'){
			$no_matches = true;
			$rejected_all = true;
		} else if(!$reset_match){
			$confirmed_user_name = $confirm_name_response;
		}
	}

	$feed = 'https://docs.google.com/spreadsheets/d/1wrctf4Rb7wgNY1wgP2FXBpfSibLSNKuUuSif2bjx8DU/pub?gid=1139113070&single=true&output=csv';

	$keys = array(); //Array for Column Titles\
	$spreadsheetRows = array(); // Final Array\
	$roomGroupings = array(); // Final Array
	$data = csvToArray($feed, ',');

	$count = count($data) - 1;

	//Store Column Titles
	$labels = array_shift($data);
	foreach ($labels as $label) {
	  $keys[] = $label;
	}

	//Creat ID's
	$keys[] = 'id';
	for ($i = 0; $i < $count; $i++) {
	  $data[$i][] = $i;
	}

	//Combine Keys and Values
	for ($j = 0; $j < $count; $j++) {
	  $d = array_combine($keys, $data[$j]);
	  $spreadsheetRows[$j] = $d;
	}

	if(!$no_matches):

		// Whatever you want to do inside the loop
		foreach($spreadsheetRows as $item){
			$item_last_name = $item['Last Name'];
			$item_email = $item['Email'];
			$item_email = $item_email && !empty($item_email) ? strtolower($item_email) : "";
			$room_num = $item['Room Number'];
			$room_key = str_replace(' ', '', strtolower($room_num));
			// store the room key for later comparison
			$item['room_key'] = $room_key;

			if(empty($roomGroupings[$room_key])){
				$roomGroupings[$room_key] = array();
			}

			array_push($roomGroupings[$room_key], $item['Preferred Name']);

			// if we already know their unique access code or Preferred Name, then we've got 'em!
			if($confirmed_user_name && $confirmed_user_name == $item['Preferred Name']){
				$confirmed_user = $item;
				update_user_meta( $curr_id, 'housing_preferred_name', $item['Preferred Name']);
			}

			// if the email address matches, we very probably have them but should still collect surnames matches
			if($item_email == $email_lc){
				array_push($narrow_matches, $item);
			}

			// otherwise, try name matching
			if(strpos(strtolower($item_last_name), strtolower($last_name_end)) > -1 || strpos(strtolower($last_name_end), strtolower($item_last_name)) > -1){
				array_push($surname_matches, $item);
			}

			if(strpos(strtolower($item_last_name), strtolower($alt_last_name_end)) > -1 || strpos(strtolower($alt_last_name_end), strtolower($item_last_name)) > -1){
				array_push($surname_matches, $item);
			}
		}



		// if we don't have an confirmed match yet, try to narrow down our name matches
		if($confirmed_user != true){
			// if we have too many matches, try to narrow down
			if(count($surname_matches) > 0){
				foreach($surname_matches as $item){
					$item_first_name = $item['First Name'];

					if(!in_array($item, $narrow_matches)){
						// first try first name
						if(strpos(strtolower($item_first_name), strtolower($first_name)) > -1 || strpos(strtolower($first_name), strtolower($item_first_name)) > -1){
							array_push($narrow_matches, $item);
						}

						// then nickname just in case
						if(strpos(strtolower($item_first_name), strtolower($nick_name)) > -1 || strpos(strtolower($nick_name), strtolower($item_first_name)) > -1){
							if(!in_array($item, $narrow_matches)){
								array_push($narrow_matches, $item);
							}
						}
					}
				}

				$num_narrow_matches = count($narrow_matches);

				if($widen_net == true){
					if(count($surname_matches) > $num_narrow_matches){
						$possible_matches = $surname_matches;
					}	else {
						$no_matches = true;
						$rejected_all = true;
					}
				} else if($num_narrow_matches > 1){
					$likely_matches = $narrow_matches;
				}	else if ($num_narrow_matches == 1){
					$probable_match = $narrow_matches[0];
				} else {
					$possible_matches = $surname_matches;
				}
			} else {
				$no_matches = true;
			}

		}
	endif;
endif;
?>


<article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>

	<h1 class="post-title"><?php

	$title = get_the_title();
	$title_formatted = '';

	$accent_text_override = get_field('accent_color_text_override');

	if($accent_text_override && $accent_text_override != '') {
		$title_formatted = '<span>' . str_replace($accent_text_override, '</span><span class="accent-color">' . $accent_text_override . '</span>', $title);
	} else {
		$title_formatted = pu06_string_to_spans($title);
	}

		if ( is_singular() ) :
			echo $title_formatted;

		else : ?>

			<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php
				echo $title_formatted; ?>
			</a><?php

		endif; ?>
	</h1>

	<div class="post-content">

		<?php if($logged_in): ?>

			<p style="margin-bottom: 48px;"><strong>Name:</strong> <?= $name_string ?><br>
			<?php if(!empty($alt_last_name)){ echo '<strong>Previous Last Name:</strong> ' . $alt_last_name . '<br>'; }  ?>
			<strong>Email:</strong> <?= $current_user->user_email ?></p>

			<?php

			// if we already have a confirmed match but they haven't
			if($confirmed_user):
				$room_number = $confirmed_user['Room Number'];
				$room_type = $confirmed_user['Room Type'];
				$room_key = $confirmed_user['room_key'];
				$full_name = $confirmed_user['Preferred Name'];
				$room_occupants = $roomGroupings[$room_key];
				$roommates = array();

				foreach($room_occupants as $name):
					if($full_name !== $name){ array_push($roommates, $name); }
				endforeach;
			?>
				<h2 class="h1" style="margin-top: 48px;"><span class="accent-color">Housing</span> Info</h2>
				<p style="margin-top: 20px;margin-bottom: 24px;"><strong>On-Campus Housing Name Match:</strong> <?= $confirmed_user['Preferred Name'] ?><br>
				<strong>Room Number:</strong> <?= $room_number ?><br>
				<strong>Room Type:</strong> <?= $room_type ?><br>
				<?php if(count($roommates) > 0): ?>
					<strong>Roommates:</strong> <?= join(',  ', $roommates) ?><br>
				<?php endif; ?>

				<?php if($confirmed_user['Code'] && !empty($confirmed_user['Code'])): ?>
					<strong>Personal Access Code:</strong> <?= $confirmed_user['Code'] ?><br>
					<em>Find out where to enter this code in our <strong><a href="/reunions-2016/#how-to-register">Step-by-Step Registration Guide.</a></strong></em></p>

					<div class="reg-info">
						<p style="margin-top: 8px;">Questions? <a href="/reunions-2016">Check out our Reunions page</a>.</p>
					</div>
				<?php endif; ?>

				<form name="confirm_identity" method="POST" class="reset_button">
					<p style="font-size: 0.65em;margin-bottom: 2px; margin-top: 36px; margin-left: 2px;">Not you? Click below to unlink this match and try again.</p>
					<input type="hidden" id="confirm_name" name="confirm_name" value="change_identity">
					<input type="submit" value="Reset my match">
				</form>

			<?php else: ?>

				<form name="confirm_identity" method="POST" class="wpmem_msg padding">

				<?php if($probable_match && !$no_more): ?>
						<p>We found a probable on-campus housing match for your information!<br>Are you <strong><?= $probable_match['Preferred Name'] ?>?</strong></p>
						<input type="radio" id="confirm_yes" name="confirm_name" value="<?= $probable_match['Preferred Name'] ?>"><label for="confirm_yes">Yup, that's me!</label><br>
						<input type="radio" id="confirm_no_bool" name="confirm_name" value="try_possible_matches"><label for="confirm_no_bool">Nope, I don't see my name</label><br>
						<input type="submit" value="Submit">

				<?php elseif($likely_matches): ?>
						<p><strong>We found more than one potential on-campus housing match for your information!</strong></p>
						<p>Are any of these people you?</p>
						<?php foreach($likely_matches as $index => $match): ?>
						 	<input type="radio" id="confirm_specific_name_<?= $index ?>" name="confirm_name" value="<?= $match['Preferred Name'] ?>"><label for="confirm_specific_name_<?= $index ?>"><?= $match['Preferred Name'] ?></label><br>
						<?php endforeach; ?>
						<input type="radio" id="confirm_no" name="confirm_name" value="try_possible_matches"><label for="confirm_no"><?= $no_text ?></label><br>
						<input type="submit" value="Submit">

				<?php elseif($possible_matches): ?>
					<?php if($widen_net): ?>
						<p><strong>Okay, let's widen the net.</strong></p>
						<p>Do any of these names belong to you?</p>
					<?php else: ?>
						<p><strong>We couldn't find any complete on-campus housing name matches for your information, but we did find some matches to your last name.</strong></p>
						<p>Are any of these people you perchance?</p>
					<?php endif; ?>
					<?php foreach($possible_matches as $index => $match): ?>
					 	<input type="radio" id="confirm_specific_name_<?= $index ?>" name="confirm_name" value="<?= $match['Preferred Name'] ?>"><label for="confirm_specific_name_<?= $index ?>"><?= $match['Preferred Name'] ?></label><br>
					<?php endforeach; ?>
					<input type="radio" id="confirm_no" name="confirm_name" value="no_possible_matches"><label for="confirm_no"><?= $no_text ?></label><br>
					<input type="submit" value="Submit">

				<?php else: ?>
						<?php if($rejected_all): ?>
							<p><strong>Hmmm...we don't have any more potential matches.</strong></p>
						<?php else: ?>
							<p><strong>We can't find any potential on-campus housing matches for your information.</strong></p>
						<?php endif; ?>

						<p>If you didn't purchase on-campus housing, then this is totally normal! If you did and
						are trying to find out your room assignment, make sure that you spelled everything correctly in your profile information
						and that any previous or maiden names are entered in the Previous Last Name field.</p>
						<p>If you're sure all of your information is correct and you're still seeing this message despite having purchased housing,
						<a href="mailto:reunionsclassof2006@gmail.com?
										subject=[HELP%20NEEDED]%20Missing%20On-Campus%20Housing%20Match:%20<?= $name_string ?>&
										body=Account%20Name:%20<?= $name_string ?>
										<?php if($alt_last_name){ echo '%0D%0APrevious%20Last%20Name:%20' . $alt_last_name; }?>
										%0D%0AProvided%20Email:%20<?= $email ?>%0D%0A%0D%0A" >contact us by email</a>
						and we'll make sure we get you hooked up to your housing info!</p>
						<input type="submit" value="Try Again">
					<?php endif; ?>

				</form>
			<?php endif; ?>

		<?php endif; ?>

		<?php

		if ( '' != get_the_post_thumbnail() ) : ?>
			<?php the_post_thumbnail(); ?><?php
		endif; ?>

		<?php if ( is_front_page() || is_category() || is_archive() || is_search() ) : ?>

			<?php the_excerpt(); ?>
			<a class="read-more" href="<?php the_permalink(); ?>"><?php _e( 'Read more &raquo;', 'pu06' ); ?></a>

		<?php else : ?>

			<?php the_content( __( 'Continue reading &raquo', 'pu06' ) ); ?>

		<?php endif; ?>

		<?php
			wp_link_pages(
				array(
					'before'           => '<div class="linked-page-nav"><p>'. __( 'This article has more parts: ', 'pu06' ),
					'after'            => '</p></div>',
					'next_or_number'   => 'number',
					'separator'        => ' ',
					'pagelink'         => __( '&lt;%&gt;', 'pu06' ),
				)
			);
		?>

	</div>

</article>
