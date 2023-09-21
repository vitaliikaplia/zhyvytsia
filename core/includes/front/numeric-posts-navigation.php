<?php

if(!defined('ABSPATH')){exit;}

function numeric_posts_nav($wp_query) {

	$html = "";

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	$html .= '<ul>' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		$html .= sprintf( '<li class="prev">%s</li>' . "\n", get_previous_posts_link( '<svg viewBox="0 0 19 32"><path d="M8.621 31.609c0.234 0.238 0.558 0.387 0.917 0.391h0.001c0.002 0 0.004 0 0.006 0 0.359 0 0.682-0.15 0.911-0.391l0.001-0 8.247-8.23c0.235-0.235 0.38-0.56 0.38-0.918 0-0.717-0.581-1.299-1.299-1.299-0.359 0-0.683 0.145-0.918 0.38v0l-6.036 6.019v-26.27c0-0.714-0.579-1.292-1.292-1.292s-1.292 0.579-1.292 1.292v0 26.27l-6.036-6.019c-0.235-0.235-0.56-0.38-0.918-0.38-0.717 0-1.298 0.581-1.298 1.299 0 0.359 0.145 0.683 0.38 0.918v0z"></path></svg>' ) );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		$html .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			$html .= '<li class="is-current"><a href="#current">...</a></li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		$html .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			$html .= '<li class="is-current"><a href="#current">...</a></li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		$html .= sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		$html .= sprintf( '<li class="next">%s</li>' . "\n", get_next_posts_link( '<svg viewBox="0 0 19 32"><path d="M8.621 31.609c0.234 0.238 0.558 0.387 0.917 0.391h0.001c0.002 0 0.004 0 0.006 0 0.359 0 0.682-0.15 0.911-0.391l0.001-0 8.247-8.23c0.235-0.235 0.38-0.56 0.38-0.918 0-0.717-0.581-1.299-1.299-1.299-0.359 0-0.683 0.145-0.918 0.38v0l-6.036 6.019v-26.27c0-0.714-0.579-1.292-1.292-1.292s-1.292 0.579-1.292 1.292v0 26.27l-6.036-6.019c-0.235-0.235-0.56-0.38-0.918-0.38-0.717 0-1.298 0.581-1.298 1.299 0 0.359 0.145 0.683 0.38 0.918v0z"></path></svg>' ) );

	$html .= '</ul>' . "\n";

	return $html;

}
