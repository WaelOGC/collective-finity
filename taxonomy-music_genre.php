<?php
/**
 * Template Name: Music Genre Archive
 * Description: Displays tracks filtered by genre
 */

get_header(); 

// Get current genre information
$current_genre = get_queried_object();
$genre_name = $current_genre->name;
$genre_description = $current_genre->description;
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <!-- Page Header -->
        <div class="genre-header" style="text-align: center; padding: 60px 5px 40px; background: linear-gradient(180deg, rgba(0,255,255,0.05) 0%, transparent 100%);">
            <h1 style="font-family: 'Mulish', sans-serif; font-size: 38px; font-weight: 700; color: #fff; margin-bottom: 12px;">
                <?php echo esc_html( $genre_name ); ?>
            </h1>
            <?php if ( $genre_description ) : ?>
                <p style="font-family: 'Space Mono', monospace; font-size: 14px; color: #888; max-width: 600px; margin: 0 auto;">
                    <?php echo esc_html( $genre_description ); ?>
                </p>
            <?php else : ?>
                <p style="font-family: 'Space Mono', monospace; font-size: 14px; color: #888; max-width: 600px; margin: 0 auto;">
                    <?php _e( 'Explore tracks in this genre, crafted with emotion and cinematic depth.', 'collective-finity' ); ?>
                </p>
            <?php endif; ?>
            
            <!-- Back link -->
            <a href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>" style="display: inline-block; margin-top: 20px; font-family: 'Space Mono', monospace; font-size: 12px; color: #FFB800; text-decoration: none; border: 1px solid rgba(255,184,0,0.3); padding: 8px 20px; border-radius: 30px; transition: all 0.3s ease;">
                ← <?php _e( 'Back to Library', 'collective-finity' ); ?>
            </a>
        </div>

        <!-- Tracks Grid -->
        <div class="genre-tracks-container" style="max-width: 1200px; margin: 0 auto; padding: 0 5px;">
            
            <?php if ( have_posts() ) : ?>
                
                <div class="tracks-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px; margin-top: 20px;">
                    
                    <?php while ( have_posts() ) : the_post();
                        
                        // Get track cover
                        $cover_image = get_post_meta( get_the_ID(), 'track_cover_url', true );
                        if ( ! $cover_image ) {
                            $cover_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                        }
                        if ( ! $cover_image ) {
                            $cover_image = collective_finity_default_art_url();
                        }
                        
                        // Get artist
                        $artists = wp_get_post_terms( get_the_ID(), 'track_artist', array( 'fields' => 'names' ) );
                        $artist_name = ! empty( $artists ) ? $artists[0] : 'Collective Finity';
                        
                        // Get BPM and Key
                        $bpm = get_post_meta( get_the_ID(), 'track_bpm', true );
                        $key = get_post_meta( get_the_ID(), 'track_key', true );
                        $show_bpm = collective_finity_track_show_bpm( get_the_ID() );
                        $show_key = collective_finity_track_show_key( get_the_ID() );
                    ?>
                    
                        <div class="track-card" style="background: #111; border: 1px solid #2a2a2a; border-radius: 14px; overflow: hidden; transition: transform 0.3s ease, border-color 0.3s ease;">
                            <a href="<?php the_permalink(); ?>" style="display: block; text-decoration: none;">
                                <!-- Track Cover -->
                                <div style="position: relative; padding-bottom: 100%; background: #0a0a0a; overflow: hidden;">
                                    <img src="<?php echo esc_url( $cover_image ); ?>" 
                                         alt="<?php the_title_attribute(); ?>" 
                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <!-- Overlay -->
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #FFB800; font-size: 48px;">▶</span>
                                    </div>
                                </div>
                                
                                <!-- Track Info -->
                                <div style="padding: 16px 18px;">
                                    <h3 style="font-family: 'Mulish', sans-serif; font-size: 16px; font-weight: 700; color: #fff; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php the_title(); ?>
                                    </h3>
                                    <p style="font-family: 'Space Mono', monospace; font-size: 12px; color: #888; margin: 0 0 10px;">
                                        <?php echo esc_html( $artist_name ); ?>
                                    </p>
                                    
                                    <?php if ( ( $show_bpm && $bpm ) || ( $show_key && $key ) ) : ?>
                                        <div style="display: flex; gap: 12px; font-family: 'Space Mono', monospace; font-size: 11px; color: #555;">
                                            <?php if ( $show_bpm && $bpm ) : ?>
                                                <span><?php echo esc_html( $bpm ); ?> BPM</span>
                                            <?php endif; ?>
                                            <?php if ( $show_key && $key ) : ?>
                                                <span><?php echo $show_bpm && $bpm ? '· ' : ''; ?><?php echo esc_html( $key ); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Genre badge -->
                                    <div style="margin-top: 10px;">
                                        <span style="display: inline-block; font-family: 'Space Mono', monospace; font-size: 10px; color: #00FFFF; border: 1px solid rgba(0,255,255,0.2); padding: 2px 12px; border-radius: 20px;">
                                            <?php echo esc_html( $genre_name ); ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    
                    <?php endwhile; ?>
                    
                </div>
                
                <!-- Pagination -->
                <div style="text-align: center; margin-top: 40px;">
                    <?php
                    the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => __( '← Previous', 'collective-finity' ),
                        'next_text' => __( 'Next →', 'collective-finity' ),
                    ) );
                    ?>
                </div>
                
            <?php else : ?>
                
                <!-- Empty state -->
                <div style="text-align: center; padding: 80px 20px;">
                    <span style="font-size: 48px; display: block; margin-bottom: 20px;">🎵</span>
                    <h2 style="font-family: 'Mulish', sans-serif; font-size: 24px; color: #fff; margin-bottom: 12px;">
                        <?php _e( 'No Tracks in This Genre', 'collective-finity' ); ?>
                    </h2>
                    <p style="font-family: 'Space Mono', monospace; font-size: 14px; color: #888;">
                        <?php _e( 'Check back soon for new music in this category.', 'collective-finity' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/tracks/' ) ); ?>" style="display: inline-block; margin-top: 20px; font-family: 'Space Mono', monospace; font-size: 12px; color: #FFB800; text-decoration: none; border: 1px solid rgba(255,184,0,0.3); padding: 8px 24px; border-radius: 30px;">
                        <?php _e( 'Browse All Music', 'collective-finity' ); ?>
                    </a>
                </div>
                
            <?php endif; ?>
            
        </div>
        
    </main>
</div>

<style>
    .track-card:hover {
        transform: translateY(-6px);
        border-color: rgba(0, 255, 255, 0.3);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
    }
    .track-card:hover .track-card-overlay {
        opacity: 1 !important;
    }
    .track-card:hover img {
        transform: scale(1.05);
    }
    .nav-links {
        display: flex;
        justify-content: center;
        gap: 12px;
        font-family: 'Space Mono', monospace;
    }
    .nav-links a, .nav-links span {
        padding: 8px 16px;
        border: 1px solid #2a2a2a;
        border-radius: 8px;
        color: #888;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .nav-links a:hover {
        border-color: #FFB800;
        color: #FFB800;
    }
    .nav-links .current {
        background: rgba(255,184,0,0.1);
        border-color: #FFB800;
        color: #FFB800;
    }
</style>

<?php get_footer(); ?>