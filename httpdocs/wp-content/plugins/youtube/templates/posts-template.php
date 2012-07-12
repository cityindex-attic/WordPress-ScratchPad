<?php
/**
 * Template Name: Youtube gallery template
 *
 */
?>

<?php get_header(); ?>
       
	<div id="content" class="clearfix" style="width: 100%;">
          

             
                 <div id="youtubeGalleryContent">
                     <h2>Gallery</h2>
         
		<?php 
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $loop = new WP_Query( array( 'post_type' => 'youtube','posts_per_page' => 5, 'paged'=>$paged, 'post_status'=>"publish" ) ); 
                ?>

                <?php while ( $loop->have_posts()) : $loop->the_post(); ?>
                        <?php 
                            $accepted = get_post_meta(get_the_ID(), "accepted", true);
                            $url = get_post_meta(get_the_ID(), "url", true);
                            $description = get_post_meta(get_the_ID(), "description", true);
                            
                            if($accepted==1){
                             $movie = explode('?v=',$url);
                            $movie = explode('&',$movie[1]);
                            $movie = $movie[0];?>
                     <div class="youtubeEntry">
                         <div class="float-left youtube-movie">
                             <iframe width="500" height="300" src="http://www.youtube.com/embed/<?php echo $movie; ?>" frameborder="0" allowfullscreen></iframe>
                         </div>
                         <div class="float-left youtube-description">
                             <h2 class="title"><?php the_title();?></h2>
                             <p><?php echo $description;?></p>
                             <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.youtube.com/watch?v=<?php echo $movie;?>">Tweet</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                            <a href="http://pinterest.com/pin/create/button/?url=http://www.youtube.com/watch?v=<?php echo $movie;?>&media=http://img.youtube.com/vi/<?php echo $movie; ?>/2.jpg&description=<?php echo $description;?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
                            <iframe id="facebook-like" scrolling="no" frameborder="0" src="//www.facebook.com/plugins/like.php?href=http://www.youtube.com/watch?v=<?php echo $movie;?>&send=false&layout=standard&width=450&show_faces=false&action=like&colorscheme=light&font=arial&height=35&appId=347305879856" style="border:none; overflow:hidden; width:200px; height:35px;" allowtransparency="true"></iframe>
                         </div>
                         <div style="clear:both"></div>
                         
                     </div>

                                    
                            

                <?php }
                endwhile; 
                $current = (intval(get_query_var('paged'))) ? intval(get_query_var('paged')) : 1;
                $rows = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'youtube' AND post_status = 'publish'");
                global $wp_rewrite;
                $args = array(
                        'base'         => @add_query_arg('paged','%#%'),
                        'format'       => '?page=%#%',
                        'total'        => ceil(sizeof($rows)/5),
                        'current'      => $current,
                        'show_all'     => False,
                        'end_size'     => 1,
                        'mid_size'     => 2,
                        'prev_next'    => True,
                        'prev_text'    => __('&laquo; Previous'),
                        'next_text'    => __('Next &raquo;'),
                        'type'         => 'plain',
                        'add_args'     => False);
                if( $wp_rewrite->using_permalinks() )
                    $args['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged');
                if( !empty($wp_query->query_vars['s']) )
                    $args['add_args'] = array('s'=>get_query_var('s'));
 
                echo paginate_links( $args );
                ?>
                     <div style="clear:both"></div>
            </div>
                        <div style="clear:both"></div>
	</div>
	<!-- /#content -->
		
<?php get_footer(); ?>
 
