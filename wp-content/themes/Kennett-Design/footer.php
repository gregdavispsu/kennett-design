<?php


function render_footer() {
    $builder_link = '<a href="http://ithemes.com/purchase/builder-theme/" title="iThemes Builder">iThemes Builder</a>';
    $ithemes_link = '<a href="http://ithemes.com/" title="iThemes WordPress Themes">iThemes</a>';
    $wordpress_link = '<a href="http://wordpress.org">WordPress</a>';

    $footer_credit = sprintf( __( 'Proudly powered by %3$s<br/>Built and Designed by <a href="http://38solutions.com/">38solutions</a>', 'it-l10n-Builder' ), $builder_link, $ithemes_link, $wordpress_link );
    $footer_credit = apply_filters( 'builder_footer_credit', $footer_credit );

?>
    <div class="alignleft">
        <strong><?php bloginfo( 'name' ); ?></strong> &middot; 117 West State Street, Kennett Square, PA 19348<br />
        <?php printf( __( 'Copyright &copy; %s All Rights Reserved', 'it-l10n-Builder' ), date( 'Y' ) ); ?>
    </div>
    <div class="alignright">
        <?php echo $footer_credit; ?>
    </div>
    <?php wp_footer(); ?>
<?php

}

add_action( 'builder_layout_engine_render_footer', 'render_footer' );