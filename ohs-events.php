<?php
/*
Plugin Name: OHS Events Plugin
Description: Events Plugin
Author: Derek Dorr
Version: 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

register_activation_hook( __FILE__, 'ohsevents_activate' );

function ohsevents_activate() {
	flush_rewrite_rules();
}

add_action('init', 'events_type');
add_action('save_post', 'ohs_events_save_postdata', 10, 2);
add_action("manage_posts_custom_column", "ohs_events_columns_content");
add_filter("manage_edit-event_columns", "ohs_events_columns_title");

function events_type() {

	$labels = array(
		'name' => _x('Events', 'post type general name'),
		'singular_name' => _x('Event', 'post type singular name'),
		'add_new' => _x('Add New', 'event'),
		'add_new_item' => __('Add New Event'),
		'edit_item' => __('Edit Event'),
		'new_item' => __('New Event'),
		'view_item' => __('View Event'),
		'search_items' => __('Search Events'),
		'not_found' =>  __('No events found'),
		'not_found_in_trash' => __('No events found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => _x('Events', 'menu name')
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 3,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array('title','editor','thumbnail'),
		'register_meta_box_cb' => 'events_boxes',
		'has_archive' => true,
		'query_var' => true,
		'show_in_rest' => true,
		'rest_base' => 'events',
		'rest_controller_class' => 'WP_REST_Posts_Controller'
	);
	
	register_post_type('event',$args);
	
}

function events_boxes() {
	add_meta_box( 'datetime', __( 'Date & Time' ), 'ohs_events_date_and_time', 'event', 'normal', 'high' );
	add_meta_box( 'place', __( 'Place' ), 'ohs_events_places', 'event', 'normal', 'high' );
}

function ohs_events_date_and_time() {

	global $post;
	
	$custom = get_post_custom($post->ID);
	
	if (!$custom == '') {
		if(isset($custom["_date_start"][0])) {
			$date_start = $custom["_date_start"][0];
		} else { $date_start = '';}
		if(isset($custom["_time_start"][0])) {
			$time_start = $custom["_time_start"][0];
		} else { $time_start = '';}
		if(isset($custom["_date_end"][0])) {
			$date_end = $custom["_date_end"][0];
		} else { $date_end = '';}
		if(isset($custom["_time_end"][0])) {
			$time_end = $custom["_time_end"][0];
		} else { $time_end = '';}
	}
	else {
		$date_start = '';
		$time_start = '';
		$date_end = '';
		$time_end = '';
	}
	
?>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="date_start"><?php _e('Start:'); ?></label></th>
		<td>
			<span class="description"><?php _e('Date:'); ?></span><input name="date_start" type="text" id="date_start" value="<?php echo $date_start; ?>" class="regular-date datepicker" />
		</td>
		<td>
			<span class="description"><?php _e('Time:'); ?></span><input name="time_start" type="text" id="time_start" value="<?php echo $time_start; ?>" class="regular-time timepicker" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="date_end"><?php _e('End:'); ?></label></th>
		<td>
			<span class="description"><?php _e('Date:'); ?></span><input name="date_end" type="text" id="date_end"  value="<?php echo $date_end; ?>" class="regular-date datepicker" />
		</td>
		<td>
			<span class="description"><?php _e('Time:'); ?></span><input name="time_end" type="text" id="time_end"  value="<?php echo $time_end; ?>" class="regular-time timepicker" />
		</td>
	</tr>
	
</table>

<?php
}

function ohs_events_places() {

	global $post;
  
	$custom = get_post_custom($post->ID);
	
	if (!$custom == '') {
		if (isset($custom["_location_name"][0])) {
			$location_name = $custom["_location_name"][0];
		}
		if (isset($custom["_location_address"][0])) {
			$location_address = $custom["_location_address"][0];
		}
		if (isset($custom["_location_town"][0])) {
			$location_town = $custom["_location_town"][0];
		}
		if (isset($custom["_location_state"][0])) {
			$location_state = $custom["_location_state"][0];
		}
		if (isset($custom["_location_zip"][0])) {
			$location_zip = $custom["_location_zip"][0];
		}
		if (isset($custom["_location_latlong"][0])) {
			$location_latlong = $custom["_location_latlong"][0];
		}
	
	}
	else {
		$location_name = '';
		$location_address = '';
		$location_town = '';
		$location_state = '';
		$location_zip = '';
		$location_latlong = '';
	}

	
	
?>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="location_name"><?php _e('Name:'); ?></label></th>
		<td>
			<input name="location_name" type="text" id="location_name" value="<?php echo $location_name; ?>" class="regular-text" />
			<span class="description"><?php _e('The name of the place.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="location_address"><?php _e('Address:'); ?></label></th>
		<td>
			<input name="location_address" type="text" id="location_address" value="<?php echo $location_address; ?>" class="regular-text" />
			<span class="description"><?php _e('The address to the place.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="location_town"><?php _e('Town:'); ?></label></th>
		<td>
			<input name="location_town" type="text" id="location_town" value="<?php echo $location_town; ?>" class="regular-text" />
			<span class="description"><?php _e('The town in which you can find the place.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="location_state"><?php _e('State:'); ?></label></th>
		<td>
			<input name="location_state" type="text" id="location_state" value="<?php echo $location_state; ?>" class="regular-text" />
			<span class="description"><?php _e('The state in which you can find the place.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="location_zip"><?php _e('Postal Code:'); ?></label></th>
		<td>
			<input name="location_zip" type="text" id="location_zip" value="<?php echo $location_zip; ?>" class="regular-text" />
			<span class="description"><?php _e('The postal code in which you can find the place.'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="location_latlong"><?php _e('Lat/Long:'); ?></label></th>
		<td>
			<input name="location_latlong" type="text" id="location_latlong" value="<?php echo $location_latlong; ?>" class="regular-text" />
			<span class="description"><?php _e('The latitude and longitude of the place.'); ?></span>
		</td>
	</tr>
	
</table>

<?php
}

function ohs_events_save_postdata( $post_id ) {

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

	if(isset($_POST['post_type'])) {
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
					return $post_id;
		}
	}

	if(isset($_POST['date_start'])) {
		update_post_meta($post_id, '_date_start', $_POST['date_start']);
	}
	if(isset($_POST['time_start'])) {
		update_post_meta($post_id, '_time_start', $_POST['time_start']);
	}
	if(isset($_POST['date_end'])) {
		update_post_meta($post_id, '_date_end', $_POST['date_end']);
	}
	if(isset($_POST['time_end'])) {
		update_post_meta($post_id, '_time_end', $_POST['time_end']);
	}
	if(isset($_POST['location_name'])) {
		update_post_meta($post_id, '_location_name', $_POST['location_name']);
	}
	if(isset($_POST['location_address'])) {
		update_post_meta($post_id, '_location_address', $_POST['location_address']);
	}
	if(isset($_POST['location_town'])) {
		update_post_meta($post_id, '_location_town', $_POST['location_town']);
	}
	
	if(isset($_POST['location_zip'])) {
		update_post_meta($post_id, '_location_zip', $_POST['location_zip']);
	}
	
	if(isset($_POST['location_state'])) {
		update_post_meta($post_id, '_location_state', $_POST['location_state']);
	}
	
	if(isset($_POST['location_latlong'])) {
		update_post_meta($post_id, '_location_latlong', $_POST['location_latlong']);
	}
  
}

function ohs_events_columns_title($columns) {
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __('Title'),
		"date_start" => "Start",
		"date_end" => "End",
		"location" => "Location",
		"date" => __('Published')
	);
	return $columns;
}

function ohs_events_columns_content($column) {
	global $post;
	$custom = get_post_custom();
	
	$dateStart = '';
	$dateEnd = '';
	$location = '';
	
	if(isset($custom["_date_start"][0])) {
		$dateStart .= $custom["_date_start"][0].'<br>';
	}
	
	if(isset($custom["_time_start"][0])) {
		$dateStart .= $custom["_time_start"][0];
	}
	
	if(isset($custom["_date_end"][0])) {
		$dateEnd .= $custom["_date_end"][0].'<br>';
	}
	
	if(isset($custom["_time_end"][0])) {
		$dateEnd .= $custom["_time_end"][0];
	}
	
	if(isset($custom["_location_name"][0])) {
		$location .= $custom["_location_name"][0].'<br>';
	}
	
	if(isset($custom["_location_address"][0])) {
		$location .= $custom["_location_address"][0].'<br>';
	}
	
	if(isset($custom["_location_town"][0])) {
		$location .= $custom["_location_town"][0].', ';
	}
	
	if(isset($custom["_location_state"][0])) {
		$location .= $custom["_location_state"][0].' ';
	}
	
	if(isset($custom["_location_zip"][0])) {
		$location .= $custom["_location_zip"][0];
	}
	
	if(isset($custom["_location_latlong"][0])) {
		$location .= '<br>'.$custom["_location_latlong"][0];
	}
	
	if ("date_start" === $column) echo $dateStart;
	elseif ("date_end" === $column) echo $dateEnd;
	elseif ("location" === $column) echo $location;
}

/**
 * Wordpress API 
 */
 
add_action( 'rest_api_init', 'ohsevents_register_api_hooks' );

function ohsevents_register_api_hooks() {

	/**
	 * Add Fields to Events API
	 */
	 
	register_rest_field( 'event', 'media', array(
		'get_callback' => 'events_register_media',
		'update_callback' => null,
		'schema' => null
	) );
	
	register_rest_field( 'event', 'dates', array(
		'get_callback' => 'events_register_dates',
		'update_callback' => null,
		'schema' => null
	) );
	
	register_rest_field( 'event', 'location', array(
		'get_callback' => 'events_register_location',
		'update_callback' => null,
		'schema' => null
	) );
}

function events_register_media($object, $field_name, $request) {
	$featuredImageId = get_post_thumbnail_id($object['id']);
	
	$media = null;
	
	if ($featuredImageId) {
		$fullSizeFeatured = wp_get_attachment_image_src( $featuredImageId, 'full', false);
		$largeFeatured = wp_get_attachment_image_src( $featuredImageId, 'large', false);
		$mediumFeatured = wp_get_attachment_image_src( $featuredImageId, 'medium', false);
		$thumbnailFeatured = wp_get_attachment_image_src( $featuredImageId, 'thumbnail', false);
		
		$media = array(
			'id' => $featuredImageId,
			'url' => $fullSizeFeatured[0],
			'width' => $fullSizeFeatured[1],
			'height' => $fullSizeFeatured[2],
			'thumbnail' => array(
				'url' => $thumbnailFeatured[0],
				'width' => $thumbnailFeatured[1],
				'height' => $thumbnailFeatured[2]
			),
			'medium' => array(
				'url' => $mediumFeatured[0],
				'width' => $mediumFeatured[1],
				'height' => $mediumFeatured[2]
			),
			'large' => array(
				'url' => $largeFeatured[0],
				'width' => $largeFeatured[1],
				'height' => $largeFeatured[2]			
			),
			'featured' => $fullSizeFeatured[0]
		);
	} else {
		$theme_options = get_option('ohs_theme_options');
		$media['featured'] = $theme_options['defaultHero'];
	}
	
	return $media;
}

function events_register_dates($object, $field_name, $request) {

	$id = $object['id'];

	$eventTimes = array();
	
	$startDate = get_post_meta($id,"_date_start",true);
	$startTime = get_post_meta($id,"_time_start",true);
	$endDate = get_post_meta($id,"_date_end",true);
	$endTime = get_post_meta($id,"_time_end",true);
	
	if(isset($startDate)) {
		$eventTimes['startDate'] = array();
		$eventTimes['startDate']['human'] = $startDate;
	}
	
	if(isset($startTime)) {
		$eventTimes['startTime'] = array();
		$eventTimes['startTime']['human'] = $startTime;
	}
	
	if (isset($startDate) && isset($startTime)) {
		$date = strtotime($startDate . ' ' . $startTime);
		$eventTimes['startDate']['monthAbbreviation'] = date('M',$date);
		$eventTimes['startDate']['monthFull'] = date('F',$date);
		$eventTimes['startDate']['monthNumber'] = date('m',$date);
		$eventTimes['startDate']['day'] = date('d',$date);
		$eventTimes['startDate']['year'] = date('Y',$date);
		$eventTimes['startTime']['hour12'] = date('g',$date);
		$eventTimes['startTime']['hour24'] = date('H',$date);
		$eventTimes['startTime']['minute'] = date('i',$date);
		$eventTimes['startTime']['meridian'] = date('A',$date);
		$eventTimes['startTime']['epoch'] = date('U',$date);
	}
	
	if(isset($endDate)) {
		$eventTimes['endDate'] = array();
		$eventTimes['endDate']['human'] = $endDate;
	}
	
	if(isset($endTime)) {
		$eventTimes['endTime'] = array();
		$eventTimes['endTime']['human'] = $endTime;
	}
	
	if (isset($endDate) && isset($endTime)) {
		$date = strtotime($endDate . ' ' . $endTime);
		$eventTimes['endDate']['monthAbbreviation'] = date('M',$date);
		$eventTimes['endDate']['monthFull'] = date('F',$date);
		$eventTimes['endDate']['monthNumber'] = date('m',$date);
		$eventTimes['endDate']['day'] = date('d',$date);
		$eventTimes['endDate']['year'] = date('Y',$date);
		$eventTimes['endTime']['hour12'] = date('g',$date);
		$eventTimes['endTime']['hour24'] = date('H',$date);
		$eventTimes['endTime']['minute'] = date('i',$date);
		$eventTimes['endTime']['meridian'] = date('A',$date);
		$eventTimes['endTime']['epoch'] = date('U',$date);
	}
	
	return $eventTimes;
	
}

function events_register_location($object, $field_name, $request) {
	$custom = get_post_custom($object['id']);
	
	$location = array();
	
	if(isset($custom["_location_name"][0])) {
		$location['name'] = $custom["_location_name"][0];
	}
	
	if(isset($custom["_location_address"][0])) {
		$location['address'] = $custom["_location_address"][0];
	}
	
	if(isset($custom["_location_town"][0])) {
		$location['town'] = $custom["_location_town"][0];
	}
	
	if(isset($custom["_location_state"][0])) {
		$location['state'] = $custom["_location_state"][0];
	}
	
	if(isset($custom["_location_zip"][0])) {
		$location['zip'] = $custom["_location_zip"][0];
	}
	
	if(isset($custom["_location_latlong"][0])) {
		$location['latlong'] = $custom["_location_latlong"][0];
	}
	
	return $location;
}

?>
