<?php
/**
 * Custom template tags for this theme.
 *
 * @package Primer
 */

if ( ! function_exists( 'primer_has_header_image' ) ) {

	/**
	 * Check if there is a header or featured image.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function primer_has_header_image() {

		/**
		 * Filter to use a post's featured image as the header image.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		$use_featured_image = (bool) apply_filters( 'primer_header_use_featured_image', true );

		return ( has_header_image() || ( $use_featured_image && has_post_thumbnail( get_queried_object() ) ) );

	}

}

if ( ! function_exists( 'primer_get_header_image' ) ) {

	/**
	 * Returns the featured image, custom header or false in this priority order.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	function primer_get_header_image() {

		$size = ( 1 === get_theme_mod( 'full_width' ) ) ? 'primer-hero-2x' : 'primer-hero';

		/**
		 * Filter the header image size.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		$size = (string) apply_filters( 'primer_header_image_size', $size );

		/**
		 * Filter to use a post's featured image as the header image.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		$use_featured_image = (bool) apply_filters( 'primer_header_use_featured_image', true );

		/**
		 * Featured Image
		 */
		if ( $use_featured_image && ( $post = get_queried_object() ) && has_post_thumbnail( $post ) ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), $size );

			if ( isset( $image[0] ) ) {

				return $image[0];

			}

		}

		/**
		 * Header Image
		 */
		if ( has_header_image() ) {

			$header = get_custom_header();

			if ( ! empty( $header->attachment_id ) ) {

				$image = wp_get_attachment_image_src( $header->attachment_id, $size );

				if ( isset( $image[0] ) ) {

					return $image[0];

				}
				
			}

			return get_header_image();

		}

	}

}

if ( ! function_exists( 'primer_paging_nav' ) ) {

	/**
	 * Display navigation to next/previous set of posts when applicable.
	 *
	 * @global WP_Query $wp_query
	 * @since  1.0.0
	 */
	function primer_paging_nav() {

		global $wp_query;

		if ( ! isset( $wp_query->max_num_pages ) || $wp_query->max_num_pages < 2 ) {

			return;

		}

		?>
		<nav class="navigation paging-navigation" role="navigation">

			<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'primer' ) ?></h1>

			<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>

				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'primer' ) ) ?></div>

			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>

				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'primer' ) ) ?></div>

			<?php endif; ?>

			</div><!-- .nav-links -->

		</nav><!-- .navigation -->
		<?php

	}

} // primer_paging_nav

if ( ! function_exists( 'primer_post_nav' ) ) {

	/**
	 * Display navigation to next/previous post when applicable.
	 *
	 * @global WP_Post $post
	 * @since  1.0.0
	 */
	function primer_post_nav() {

		global $post;

		$previous = is_attachment() ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {

			return;

		}

		?>
		<nav class="navigation post-navigation" role="navigation">

			<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'primer' ) ?></h1>

			<div class="nav-links">

			<?php if ( is_rtl() ) : ?>

				<div class="nav-next"><?php next_post_link( '%link &larr;' ) ?></div>

				<div class="nav-previous"><?php previous_post_link( '&rarr; %link' ) ?></div>

			<?php else : ?>

				<div class="nav-previous"><?php previous_post_link( '&larr; %link' ) ?></div>

				<div class="nav-next"><?php next_post_link( '%link &rarr;' ) ?></div>

			<?php endif; ?>

			</div><!-- .nav-links -->

		</nav><!-- .navigation -->
		<?php

	}

} // primer_post_nav

if ( ! function_exists( 'primer_posted_on' ) ) {

	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 1.0.0
	 */
	function primer_posted_on() {

		$time = sprintf(
			'<time class="entry-date published" datetime="%s">%s</time>',
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {

			$time = sprintf(
				'<time class="updated" datetime="%s">%s</time>',
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

		}

		printf(
			'<span class="posted-on"><a href="%s" rel="bookmark">%s</a><span>',
			get_permalink(),
			$time // xss ok
		);

	}

} // primer_posted_on

if ( ! function_exists( 'primer_post_format' ) ) {

	/**
	 * Prints the post format for the current post.
	 *
	 * @since 1.0.0
	 */
	function primer_post_format() {

		$format = get_post_format();
		$format = empty( $format ) ? 'standard' : $format;

		printf( '<span class="post-format">%s</span>', esc_html( $format ) );

	}

} // primer_post_format

if ( ! function_exists( 'primer_breadcrumbs' ) ) {

	/**
	 * Display very simple breadcrumbs.
	 *
	 * Adapted from Christoph Weil's Really Simple Breadcrumb plugin.
	 *
	 * @link https://wordpress.org/plugins/really-simple-breadcrumb/
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Post $post
	 */
	function primer_breadcrumbs() {

		global $post;

		$separator = ' <span class="sep"></span> ';

		echo '<div class="breadcrumbs">';

		if ( ! is_front_page() ) {

			printf(
				'<a href="%s">%s</a>%s',
				esc_url( home_url() ),
				esc_html( get_bloginfo( 'name' ) ),
				$separator // xss ok
			);

			if ( 'page' === get_option( 'show_on_front' ) ) {

				printf(
					'<a href="%s">%s</a>%s',
					esc_url( primer_get_posts_url() ),
					esc_html__( 'Blog', 'primer' ),
					$separator // xss ok
				);

			}

			if ( is_category() || is_single() ) {

				the_category( ', ' );

				if ( is_single() ) {

					echo $separator; // xss ok

					the_title();

				}

			} elseif ( is_page() && $post->post_parent ) {

				$home = get_page( get_option( 'page_on_front' ) );

				for ( $i = count( $post->ancestors )-1; $i >= 0; $i-- ) {

					if ( ( $home->ID ) != ( $post->ancestors[$i] ) ) {

						echo '<a href="' . get_permalink( $post->ancestors[$i] ) . '">' . get_the_title( $post->ancestors[$i] ) . '</a>' . $separator;

					}
				}

				echo the_title();

			} elseif ( is_page() ) {

				echo the_title();

			} elseif ( is_404() ) {

				echo '404';

			}

		} else {

			bloginfo( 'name' );

		}

		echo '</div>';

	}

} // primer_breadcrumbs
