<?php
/*
Plugin Name: WPUBG
Plugin URI: https://janbpunkt.de
Description: Display your PUBG stats of the current season as a widget.
Version: 0.1
Author: Jan B-Punkt
Author URI: https://janbpunkt.de
License: none
*/


// The widget class
class WPUBG_Widget extends WP_Widget {

    // Main constructor
    public function __construct() {
        parent::__construct(
            'wpubg',
            __( 'WPUBG Widget', 'text_domain' ),
            array(
            'customize_selective_refresh' => true,
            )
        );
    }
  

    // The widget form (for the backend )
    public function form( $instance ) {
        // Set widget defaults
        $defaults = array(
            'title' => '',
            'player'    => '',
            'apikey' => '',
            'gamemode' => ''
        );
        
        // Parse current settings with defaults
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

        <?php // Widget Title ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <?php // Player Name ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'player' ) ); ?>"><?php _e( 'Ingame name', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'player' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'player' ) ); ?>" type="text" value="<?php echo esc_attr( $player ); ?>" />
        </p>
        <?php // API Key ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'apikey' ) ); ?>"><?php _e( 'Your PUBG API key', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'apikey' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'apikey' ) ); ?>" type="text" value="<?php echo esc_attr( $apikey ); ?>" />
        </p>

        <?php // GameMode ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'gamemode' ); ?>"><?php _e( 'Select game mode', 'text_domain' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'gamemode' ); ?>" id="<?php echo $this->get_field_id( 'gamemode' ); ?>" class="widefat">
            <?php
            // Your options array
            //duo, duo-fpp, solo, solo-fpp, squad, squad-fpp
            $options = array(
                ''        => __( 'Select', 'text_domain' ),
                'solo' => __( 'Solo', 'text_domain' ),
                'solo-fpp' => __( 'Solo FPP', 'text_domain' ),
                'duo' => __( 'Duo', 'text_domain' ),
                'duo-fpp' => __( 'Duo FPP', 'text_domain' ),
                'squad' => __( 'Squad', 'text_domain' ),
                'squad-fpp' => __( 'Suqad FPP', 'text_domain' ),
            );

            // Loop through options and add each one to the select dropdown
            foreach ( $options as $key => $name ) {
                echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';

            } ?>
            </select>
        </p>



        <?php 
    }

    // Update widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['player']   = isset( $new_instance['player'] ) ? wp_strip_all_tags( $new_instance['player'] ) : '';
        $instance['apikey']   = isset( $new_instance['apikey'] ) ? wp_strip_all_tags( $new_instance['apikey'] ) : '';
        $instance['gamemode']   = isset( $new_instance['gamemode'] ) ? wp_strip_all_tags( $new_instance['gamemode'] ) : '';
        return $instance;
    }

    public function widget( $args, $instance ) {

        extract( $args );

        // Check the widget options
        $title      = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $player     = isset( $instance['player'] ) ? $instance['player'] : '';
        $apikey     = isset( $instance['apikey'] ) ? $instance['apikey'] : '';
        $gamemode   = isset( $instance['gamemode'] ) ? $instance['gamemode'] : '';
    
        // WordPress core before_widget hook (always include )
        echo $before_widget;

        // Display the widget
        include "wpubg_functions.php";
        
        //find current season - easier code possible
        $url = "https://api.pubg.com/shards/steam/seasons";
        $result = getData ($url, $apikey);
        //echo $result;
        $json = json_decode($result, true);
        $seasons[] = $json;
        foreach ($seasons as $season) {
            foreach ($season as $item) {
                foreach ($item as $data) {
                    if ($data['attributes']['isCurrentSeason'] == "1") {
                        $seasonID = $data['id'];

                        break 2;
                    }
                }

            }
        }

        //get player id
        $url = "https://api.pubg.com/shards/pc-eu/players?filter[playerNames]=".$instance['player'];
        $result = getData($url, $apikey);
        //echo "result: ".$result;
        $json = json_decode($result,true);
        //print_r ($json);
        $playerID =  $json['data'][0]['id'];
        

        //get stats for the player from current season  
        $url = "https://api.pubg.com/shards/steam/players/".$playerID."/seasons/".$seasonID;
        $result = getData ($url, $apikey);
        $json = json_decode($result, true);

        $rankPoints = round($json['data']['attributes']['gameModeStats'][$gamemode]['bestRankPoint'],0);
        $wins = $json['data']['attributes']['gameModeStats'][$gamemode]['wins'];
        $top10s = $json['data']['attributes']['gameModeStats'][$gamemode]['top10s'];
        $kills = $json['data']['attributes']['gameModeStats'][$gamemode]['kills'];
        $headshotKills = $json['data']['attributes']['gameModeStats'][$gamemode]['headshotKills'];
        $losses = $json['data']['attributes']['gameModeStats'][$gamemode]['losses'];
        $roundMostKills = $json['data']['attributes']['gameModeStats'][$gamemode]['roundMostKills'];
        $roundsPlayed = $json['data']['attributes']['gameModeStats'][$gamemode]['roundsPlayed'];
        $rank = getRank($rankPoints);
        
        //open widget div
        echo '<div class="widget-text wp_widget_plugin_box">';
        
        //show widget title
		if ( $title ) {
			echo $before_title . $title . $after_title;
        }
        
        //here goes the beatuy stuff
        $mode = array (
                'solo' => 'Solo',
                'solo-fpp' => 'Solo FPP',
                'duo' => 'Duo',
                'duo-fpp' => 'Duo FPP',
                'suqad' => 'Suqad',
                'squad-fpp' => 'Squad FPP',
        );
        echo '
            <div style="background-color:#FFBF00; padding:10px;">
                <div style="float:left;"><strong>'.$mode[$gamemode].'</strong></div>
                <div style="float:right;">'.$roundsPlayed.' Games</div>
                <div style="clear:both;"></div>
            </div>
            <div style="text-align:center;">
                <p style="padding:5px;"><h3 style="margin:0px;padding:0px;">'.$player.'</h3></p>
                <img src="'.plugin_dir_url(__FILE__).'/gfx/'.strtolower($rank).'.png" width="120">
                <p style="padding:5px;"><strong>'.$rank.'</strong></p>
            </div>
            <table>
                <tr>
                    <th>Points: </th><td>'.$rankPoints.'</td>
                </tr>
                <tr>
                    <th>Rounds won: </th><td>'.$wins.'</td>
                </tr>
                <tr>
                    <th>Rounds Top10: </th><td>'.$top10s.'</td>
                </tr>
                <tr>
                    <th>Kills: </th><td>'.$kills.'</td>
                </tr>
                <tr>
                    <th>Most kills per Round: </th><td>'.$roundMostKills.'</td>
                </tr>
            </table>
        ';


        /*echo "Player ID is: ".$player."<br>";
        echo "API-URL is: ".$url;
        echo "<br>RankPoints for Squad-FPP are: ".$rankPoints."<br>";
        echo "Your rank is: ".getRank($rankPoints);*/
        
        
        //close widget div
        echo '</div>';
        
        // WordPress core after_widget hook (always include )
        echo $after_widget;
        

        
    }
}

// Register the widget
function my_register_custom_widget() {
	register_widget( 'WPUBG_Widget' );
}
add_action( 'widgets_init', 'my_register_custom_widget' );