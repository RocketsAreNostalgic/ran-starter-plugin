<?php
/**
 * Slider template Template
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

//phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.PHP.YodaConditions.NotYoda
$args = array(
	'post_type' => 'testimonial',
	'post_status' => 'publish',
	'posts_per_page' => 5,
	'meta_query' => array(
		array(
			'key' => '_ran_testimonial_key',
			'value' => 's:8:"approved";i:1;s:8:"featured";i:1;',
			'compare' => 'LIKE',
		),
	),
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) :
	$i = 1;

	echo esc_html( '<div class="ac-slider--wrapper"><div class="ac-slider--container"><div class="ac-slider--view"><ul>' );

	while ( $query->have_posts() ) :
		$query->the_post();
		$name = get_post_meta( get_the_ID(), '_ran_testimonial_key', true )['name'] ?? '';

		echo '<li class="ac-slider--view__slides' . ( $i === 1 ? ' is-active' : '' ) . '"><p class="testimonial-quote">"' . esc_html( get_the_content() ) . '"</p><p class="testimonial-author">~ ' . esc_html( $name ) . ' ~</p></li>';

		$i++;
	endwhile;

	echo esc_html( '</ul></div><div class="ac-slider--arrows"><span class="arrow ac-slider--arrows__left">&#x3c;</span><span class="arrow ac-slider--arrows__right">&#x3e;</span></div></div></div>' );

//phpcs:enable

endif;

wp_reset_postdata();
