<?php
/**
 * Template Name: Youtube upload template
 *
 */
?>

<?php get_header(); ?>
       
	<div id="content" class="clearfix" style="width: 100%;">
            <div id="uploadBox">
                <?php
                    $queried_post = get_post($post->ID);
                    $url= get_post_meta($post->ID, 'play_featured_video'); 
                     $movie = explode('?v=',$url[0]);
                    $movie = explode('&',$movie[1]);
                    $movie = $movie[0];
                 ?>
                <div class="wrapper">
                <div id="contentHeader">
                <?php
                     if($url[0]!=""){
                ?>
                <div id="featuredYoutube">
                    <iframe width="386" height="230" src="http://www.youtube.com/embed/<?php echo $movie; ?>" frameborder="0" allowfullscreen></iframe>
                </div>
               <?php } ?> 
                    <div class="values">
                        <?php echo $queried_post->post_content; ?>
                        <div id="uploadYoutube">
                            <a href="#" title="upload video">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/upload.jpg" alt=""/>
                            </a>
                        </div>
                    </div>

             
                 <div id="youtubeBox">
                     <div class="float-left">
                     <h2>RECENT VIDEOS</h2>
                     </div>
                     <div id="see-gallery" class="float-right" >
                        <?php wp_nav_menu(array('menu'=>'see-gallery'));?>
                     </div>
                     <div style="clear:both"></div>
		<?php $loop = new WP_Query( array( 'post_type' => 'youtube','posts_per_page' => 10 ) ); ?>

                <?php $i = 0;  while ( $loop->have_posts() && $i < 6) : $loop->the_post(); ?>
                        <?php 
                            $accepted = get_post_meta(get_the_ID(), "accepted", true);
                            $url = get_post_meta(get_the_ID(), "url", true);
                            $description = get_post_meta(get_the_ID(), "description", true);
                            
                            if($accepted==1){
                             $movie = explode('?v=',$url);
                            $movie = explode('&',$movie[1]);
                            $movie = $movie[0];
                               
                                    ?>
                            
                                <a id="video_<?php echo $i;?>" class="recentYoutube thickbox" href="#" title="<?php the_title_attribute();?>">
                                <img src="http://img.youtube.com/vi/<?php echo $movie; ?>/2.jpg" />
                                </a>
                     <div style="display:none;">
                         <a id="video_<?php echo $i;?>_run" class="thickbox" href="#TB_inline?height=360&width=520&inlineId=video_<?php echo $i;?>_wrapper&modal=false" title="<?php the_title_attribute();?>">show</a>
                         <div id="video_<?php echo $i;?>_wrapper" >
                              <iframe style="margin-top:53px;margin-left: 10px;" width="500" height="300" src="http://www.youtube.com/embed/<?php echo $movie; ?>" frameborder="0" allowfullscreen></iframe>
                         </div>
                     </div>
                            <?php
                                }    
                            ?>

                <?php $i++; endwhile; ?>
                     <div style="clear:both"></div>
                 </div>
                   </div>
                </div>
            </div>
                        <div style="clear:both"></div>
	</div>
	<!-- /#content -->
		
<?php get_footer(); ?>
