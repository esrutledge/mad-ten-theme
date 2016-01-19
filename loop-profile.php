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

$first_name = $current_user->first_name;
$nick_name = $current_user->nickname;
$last_name = $current_user->last_name;

$name_string = $first_name . ' ' . $last_name;
if($nick_name && !empty($nick_name)){
	$name_string .= " (" . $nick_name . ")";
}

$email = $current_user->user_email;
$email_lc = $email && !empty($email) ? strtolower($email) : "";

$alt_last_name = get_user_meta($curr_id, 'alt_last_name');
$alt_last_name = $alt_last_name && !empty($alt_last_name) ? $alt_last_name[0] : "";

$points_id = get_user_meta($curr_id, 'points_id');
$points_user = get_user_meta($curr_id, 'points_user');

$surname_matches = array();
$narrow_matches = array();
$exact_match = false;

$confirmed_user = false;

$feed = 'https://docs.google.com/spreadsheets/d/16TaJBJ3_zSyuxDQb1DRxDUdtIc5cFP-UEaYNd3ygSNw/pub?gid=762918669&single=true&output=csv';

$keys = array(); //Array for Column Titles\
$myArr = array(); // Final Array
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
  $myArr[$j] = $d;
}

// Whatever you want to do inside the loop
foreach($myArr as $item){
	$item_name = $item['Preferred Name'];
	$item_email = $item['Email'];
	$item_email = $item_email && !empty($item_email) ? strtolower($item_email) : "";


	// if we already know their points ID, then get outta here
	if($points_id && !empty($points_id) && $points_id[0] == $item['id']){
		array_push($narrow_matches, $item);
		$exact_match = true;
		$confirmed_user = $item;
		// echo "yayyy first try";	
	}

	// if the email address matches, we got 'em!
	elseif($item_email == $email){
		array_push($narrow_matches, $item);
		$exact_match = true;
		$confirmed_user = $item;
	} 

	// otherwise, try name matching
	else {
		if(strpos($item_name, $last_name) > -1){
			array_push($surname_matches, $item);
		}

		if(strpos($item_name, $alt_last_name) > -1) {
			array_push($surname_matches, $item);
		} 
	}
}

// if we don't have an confirmed match yet, try to narrow down our name matches
if($exact_match != true){
	// if we have too many matches, try to narrow down
	if(count($surname_matches) > 0){
		foreach($surname_matches as $match_item){
			$match_name = $match_item['Preferred Name'];

			// first try first name
			if(strpos($match_name, $first_name) > -1){
				array_push($narrow_matches, $match_item);
			}

			// then nickname just in case
			if(strpos($match_name, $nick_name) > -1){
				array_push($narrow_matches, $match_item);
			}
		}
	} 


	if(count($narrow_matches) == 1){
		// update_user_meta( $curr_id, 'test_field', $narrow_matches[0]['id']);
		update_user_meta( $curr_id, 'points_id', $narrow_matches[0]['id']);
		update_user_meta( $curr_id, 'points_preferred_name', $narrow_matches[0]['Preferred Name']);
		// echo "<br>";
		// var_dump($narrow_matches[0]['id']);
		// echo "<br>";
		// echo "<br>";
		// echo "-------points_id-----<br>";

		// var_dump(get_user_meta($curr_id, 'points_id'));
	}	
}
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

	<!-- <div class="post-meta"><?php
		pu06_post_meta(); ?>
	</div> -->

	<div class="post-content">

		<p><strong>Name:</strong> <?= $name_string ?><br>
			<strong>ID:</strong> <?= $confirmed_user['id'] ?><br>
		<?php if(!empty($alt_last_name)){ echo '<strong>Previous Last Name:</strong> ' . $alt_last_name; }  ?><br>
		<strong>Email:</strong> <?= $current_user->user_email ?></p>

		<?php if(!$exact_match): ?>
			<form name="confirm_identity" method="POST">
				<p>We found an '06 Points match for your information!<br>Are you <strong><?= $confirmed_user['Preferred Name'] ?>?</strong></p>
				<input type="radio" id="confirm_yes" name="confirm_name" value="true"><label for="confirm_yes">Yup, that's me!</label><br>
				<input type="radio" id="confirm_no" name="confirm_name" value="false"><label for="confirm_no">Nope.</label><br>
				<input type="submit" value="Submit">
			</form>
		<?php else: ?>
			<!-- <h2 class="h1"><span class="accent-color">Points</span> Info</h2> -->
			<p><strong>Points Total:</strong> <?= $confirmed_user['Total Points'] ?><br>
			<strong>Registration Tier:</strong> <?= $confirmed_user['Tier'] ?><br>
			<strong>Personal Access Code:</strong> <?= $confirmed_user['Access Code'] ?></p>
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
