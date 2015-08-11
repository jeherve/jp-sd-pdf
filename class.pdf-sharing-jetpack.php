<?php

if ( class_exists( 'Share_Twitter' ) && ! class_exists( 'Share_PDF' ) ) :

// Build button
class Share_PDF extends Share_Twitter {
	var $shortname = 'post-pdf';
	var $genericon = '\f440';
	public function __construct( $id, array $settings ) {
		parent::__construct( $id, $settings );
		$this->smart = 'official' == $this->button_style;
		$this->icon = 'icon' == $this->button_style;
		if ( ! $this->button_style ) {
			$this->button_style = 'icon-text';
		}
	}

	public function get_name() {
		return __( 'PDF', 'pdf_sd_jp' );
	}

	public function get_display( $post ) {
		return sprintf(
			'<a rel="nofollow" data-shared="sharing-pdf-%s" class="share-pdf sd-button share-%s" href="%s" title="%s"><span%s>%s</span></a>',
			$post->ID,
			$this->button_style,
			esc_url( add_query_arg(
				array(
					'format' => 'pdf',
				),
				get_permalink( $post->ID )
			) ),
			__( 'Click to download a copy of this post', 'pdf_sd_jp' ),
			( 'icon' == $this->button_style ) ? '></span><span class="sharing-screen-reader-text"' : '',
			_x( 'PDF', 'share to', 'pdf_sd_jp' )
		);
	}

}

endif; // class_exists
