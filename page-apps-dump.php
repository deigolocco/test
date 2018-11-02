<?php
    @set_time_limit( 0 );
	@ini_set( 'memory_limit', -1 );
    @ini_set( 'max_execution_time', 0 );

    function bs_app_print($app_id) {
        $utm = get_field('download_button_link', $app_id);
        $pkg = get_field('package_name', $app_id);

        $utm = str_replace('ap-', '', $utm);
        $utm = explode('=', $utm);
        $ut = $utm[0] . '=ap-' . $utm[1];

        update_post_meta( $app_id, 'download_button_link', $ut );
        bs_app_register($app_id, 'ap-' . $utm[1], $pkg);
    }

    function bs_app_register($i, $u, $p) {
        $permalink = 'http://bluestacks-cloud.appspot.com/auto_add_campaign?campaign_name=' . $u . '&app_pkg=' . $p;
        $remote    = wp_remote_get( $permalink, array('timeout' => 120) );


        if (is_wp_error( $remote ) ) {
            return;
        }

        $post_content = $remote['body'];
        $json = json_decode($post_content);

        var_dump($json);

        delete_post_meta( $i, 'info');
        update_post_meta( $i, 'info', $json->info );

        delete_post_meta( $i, 'success');
        update_post_meta( $i, 'success', $json->success );
    }

    $args = array(
      'post_type'      => 'app_page',
      'posts_per_page' => -1,
      'post_status'    => 'publish'
    );

    $query = bs_get_query($args);

    if($query->have_posts() ) :
        while ($query->have_posts()) : $query->the_post();

            bs_app_print($post->ID);

        endwhile;
    endif;
    wp_reset_postdata();

    echo 'acabou';
    exit;
