<?php
/*
Plugin Name: ACF Latest Posts
Plugin URI: https://github.com/neevalex/acf-latest-posts
Description: Replacement for a bugged "Latest Posts" block of the default WP editor. Requires ACF Pro plugin to run properly.
Version: 0.0.1
Author: neevalex
Author URI: https://neevalex.com
License: GPLv2 or later
Text Domain: acf-latest-posts
*/

class ACFLatestPosts {


	public function __construct() {
		 add_action( 'acf/init', array( $this, 'acf_init' ) );
	}


	public function acf_init() {
		acf_register_block(
			array(
				'name'            => 'acf_latest_posts',
				'title'           => __( 'ACF Latest Posts' ),
				'description'     => __( 'Latest Posts Block that actually work as it should.' ),
				'render_callback' => array( $this, 'render_posts' ),
				'category'        => 'main',
				'icon'            => 'media-document',
				'keywords'        => array( 'Latest Posts', 'Posts' ),
			)
		);

		if ( function_exists( 'acf_add_local_field_group' ) ) :

			acf_add_local_field_group(
				array(
					'key'      => 'group_603917137cc3b',
					'title'    => 'Block: ACF Latest Posts',
					'fields'   => array(
						array(
							'key'   => 'field_6039172ed808c',
							'label' => 'Shuffle Results?',
							'name'  => 'acf_latest_posts_shuffle',
							'type'  => 'true_false',
						),
						array(
							'key'           => 'field_603917598182d',
							'label'         => 'Limit',
							'name'          => 'acf_latest_posts_limit',
							'type'          => 'number',
							'default_value' => 3,
						),
						array(
							'key'           => 'field_6039176f8182e',
							'label'         => 'Categories',
							'name'          => 'acf_latest_posts_categories',
							'type'          => 'taxonomy',
							'taxonomy'      => 'category',
							'field_type'    => 'multi_select',
							'return_format' => 'id',
						),
					),
					'location' => array(
						array(
							array(
								'param'    => 'block',
								'operator' => '==',
								'value'    => 'acf/acf-latest-posts',
							),
						),
					),
				)
			);

		endif;
	}

	public function render_posts() {
		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => get_field( 'acf_latest_posts_limit' ),
			'tax_query'      => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => get_field( 'acf_latest_posts_categories' ),
				),
			),
			'order'          => 'DESC',
			'orderby'        => 'ID',
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			$posts = (array) $the_query->posts;
			if ( get_field( 'acf_latest_posts_shuffle' ) ) {
				shuffle( $posts );
			}
		}

		if ( $posts ) :  ?>
			<ul class="wp-block-latest-posts wp-block-latest-posts__list is-grid columns-3">
				<?php foreach ( $posts as $post ) : ?>
					<?php $this->render_post( $post->ID ); ?>
				<?php endforeach; ?>
			</ul>
			<?php
		endif;

		?>

		<?php
	}

	public function render_post( $id ) {
		?>
		<li>
			<div class="wp-block-latest-posts__featured-image aligncenter">
				<a target="_blank" href="<?php echo esc_url( get_the_permalink( $id ) ); ?>">
					<?php echo get_the_post_thumbnail( $id, 'thumbnail' ); ?>
				</a>
			</div>
			<a target="_blank" href="<?php echo esc_url( get_the_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a>
		</li>
		<?php
	}
}


$acf_latest_posts = new ACFLatestPosts();
