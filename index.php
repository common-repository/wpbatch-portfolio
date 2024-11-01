<?php 
/*
Plugin Name: WPBatch Portfolio
Plugin URI: http://dreamwebit.com/portfolio
Description: This plugin will help you to show your portfolios by Grid Layout
Author: MTM Sujan
Version: 1.0
Author URI: http://dreamwebit.com/portfolio
*/



function wpbatch_portfolio_jquery_register() {
	wp_enqueue_script('jquery');
}
add_action('init', 'wpbatch_portfolio_jquery_register');
add_filter('widget_text', 'do_shortcode');
function wpbatch_portfolio_external_files() {
    wp_enqueue_script( 'wpbatch-portfolio-masonry', plugins_url( '/js/jquery.masonry.min.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_script( 'wpbatch-portfolio-gpCarousel', plugins_url( '/js/jquery.gpCarousel.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_script( 'wpbatch-portfolio-mainjs', plugins_url( '/js/wpbatch-portfolio.js', __FILE__ ), array('jquery'), 1.0, false);
    wp_enqueue_script( 'wpbatch-portfolio-prettyPhoto', plugins_url( '/js/jquery.prettyPhoto.js', __FILE__ ), array('jquery'), 1.0, false);
	
	
    wp_enqueue_style( 'wpbatch-portfolio-style', plugins_url( '/css/style.css', __FILE__ ));
    wp_enqueue_style( 'wpbatch-portfolio-scrollpane', plugins_url( '/css/jquery.jscrollpane.css', __FILE__ ));
    wp_enqueue_style( 'wpbatch-portfolio-reset', plugins_url( '/css/reset.css', __FILE__ ));
    wp_enqueue_style( 'wpbatch-portfolio-prettyPhotocss', plugins_url( '/css/prettyPhoto.css', __FILE__ ));
}
add_action('wp_enqueue_scripts','wpbatch_portfolio_external_files');

function fonts_for_portfolio(){
?>
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow&v1' rel='stylesheet' type='text/css' />
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css' />
<link href='http://fonts.googleapis.com/css?family=Ovo' rel='stylesheet' type='text/css' />
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function(){
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
            theme:'light_rounded',
            social_tools:false,
            deeplinking:false,      
    });
  });
</script>
<style>
	p.subline a {
		pointer-events:none !important;
	}
</style>
<?php
}
add_action('wp_head', 'fonts_for_portfolio');


function custom_wpbatch_Portfolio() {
  $labels = array(
    'name'               => _x( 'WPBatch Portfolio', 'wpbatchportfolio' ),
    'singular_name'      => _x( 'Slider', 'wpbatchportfolio' ),
    'add_new'            => _x( 'Add New', 'Portfolio' ),
    'add_new_item'       => __( 'Add New Portfolio' ),
    'edit_item'          => __( 'Edit Portfolio' ),
    'new_item'           => __( 'New Portfolio' ),
    'all_items'          => __( 'All Portfolios' ),
    'view_item'          => __( 'View Portfolios' ),
    'search_items'       => __( 'Search Portfolios' ),
    'not_found'          => __( 'No Portfolio found' ),
    'not_found_in_trash' => __( 'No Portfolio found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'WPBatch Portfolio'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Holds our Simple Portfolios',
    'public'        => true,
    'menu_position' => 20,
    'menu_icon'     => plugins_url( '/images/icon.jpg', __FILE__ ),
    'supports'      => array( 'title', 'editor', 'thumbnail'),
    'has_archive'   => true,
  );
  register_post_type( 'wpbatchportfolio', $args ); 
}
add_action( 'init', 'custom_wpbatch_portfolio' );



// taxonomy 
add_action( 'init', 'create_portfolio_category', 0 );
function create_portfolio_category() {
    register_taxonomy(
        'wpbatch_portfolio',
        'wpbatchportfolio',
        array(
            'labels' => array(
                'name' => 'Categories',
                'add_new_item' => 'Add New Category',
                'new_item_name' => "New Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}



// plugin options 



function wpbatch_portfolio_shortcode($atts, $content=null){
global $wpbatch_portfolio_option;
	$query = new WP_Query( array(
        'post_type' => 'wpbatchportfolio',
        'posts_per_page' => $wpbatch_portfolio_option['portfolio_count']
    ) );
    if ( $query->have_posts() ) { ?>
		<div class="container" id="container">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="item block" data-bgimage="<?php
$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'full');
echo $imgsrc[0];
?>">
			<div class="thumbs-wrapper">
				<div class="thumbs" style="width:auto !important;">
					<a href="<?php
					$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'full');
					echo $imgsrc[0]; ?>" rel="prettyPhoto" title=""><img src="<?php
					$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'full');
					echo $imgsrc[0]; ?>" alt="<?php the_title(); ?>" /></a>
				</div>
			</div>
			<h2 class="title"><?php the_title(); ?></h2>
			<p class="subline"><?php the_terms( $post->ID, 'wpbatch_portfolio' , '' ); ?>
			</p>
			<div class="intro">
				<?php echo get_the_excerpt(); ?>
			</div>
			<div class="project-descr">
				<?php the_content(); ?>
			</div>
		</div>

		<?php endwhile;
		wp_reset_postdata(); ?>
		</div>
    <?php return $myvariable;
    }
}
add_shortcode('wpbatch_portfolio', 'wpbatch_portfolio_shortcode');



// plugin options 
add_action('admin_menu', 'wpbatch_portfolio_options_page');

function wpbatch_portfolio_options_page() {
	add_submenu_page('edit.php?post_type=wpbatchportfolio', 'Portfolio Options', 'Portfolio Options', 'administrator', basename(__FILE__), 'wpbatch_portfolio_options_display');
}


$wpbatch_portfolio_option = get_option('wpbatch_portfolio_option');
function wpbatch_portfolio_options_display(){
	global $wpbatch_portfolio_option;
	ob_start();
?>
<form action="options.php" method="POST">
<?php settings_fields('wpbatch_portfolio_group'); ?>
<h1>Portfolio Options<h1>
<?php settings_errors(); ?>
<hr />
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="width">Portfolios to show</label></th>
<td><input name="wpbatch_portfolio_option[portfolio_count]" value="<?php echo $wpbatch_portfolio_option['portfolio_count']; ?>" class="regular-text" type="number"></td> 
</tr>
<tr>
<td><input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit"></td>
</tr>
</tbody></table>
</form>
<?php
echo ob_get_clean();
}
function third_step_portfolio(){
	register_setting('wpbatch_portfolio_group', 'wpbatch_portfolio_option');
}
add_action('admin_init', 'third_step_portfolio');


?>