<?php
/**
 * The [isotope] shortcode of the plugin.
 *
 * @link       http://www.wannathemes.com
 * @since      1.0.0
 *
 * @package    Wanna_Isotope
 * @subpackage Wanna_Isotope/public
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Wanna_Isotope Shortcode Class
 *
 * @package Wanna_Isotope_Shortcode
 * @author  Juan Javier Moreno <hello@wannathemes.com>
 *
 * @since 1.0.0
 */
class Wanna_Isotope_Shortcode {

	/**
	 * Add shortcode
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        $this->plugin_name = 'wanna-isotope';

		// Register shortcode
		add_shortcode( 'isotope', array( $this, 'shortcode_isotope' ) );

        // Register scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts') );

	}

    /**
     * Registers scripts and styles for later enqueuing by the shortcake
     *
     * @since 1.1.0
     *
     * @return void
     */
    public function register_scripts(){
        wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wanna-isotope.css', array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/wanna-isotope.css' ), 'all' );
        wp_register_script( $this->plugin_name . 'isotope', plugin_dir_url( __FILE__ ) . 'js/isotope.pkgd.min.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/isotope.pkgd.min.js' ), true );
        wp_register_script( $this->plugin_name . 'isotope-cells-by-row', plugin_dir_url( __FILE__ ) . 'js/isotope.cells-by-row.js', array( $this->plugin_name . 'isotope' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/isotope.cells-by-row.js' ), true );
        wp_register_script( $this->plugin_name . 'imagesloaded', plugin_dir_url( __FILE__ ) . 'js/imagesloaded.pkgd.min.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/imagesloaded.pkgd.min.js' ), true );
        wp_register_script( $this->plugin_name . 'isotope-init', plugin_dir_url( __FILE__ ) . 'js/isotope.init.js', array( $this->plugin_name . 'isotope', $this->plugin_name . 'isotope-cells-by-row', $this->plugin_name . 'imagesloaded', 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/isotope.init.js' ), true );
    }

	/**
	 * Isotope output
	 *
	 * Retrieves a media files and settings to display a video.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $atts Shortcode attributes
	 */
	public function shortcode_isotope( $atts ) {

        extract( shortcode_atts( array(
            'id'        => '',
            'class'     => '',
            'type'      => 'post',
            'items'     => 4,
            'order'     => '',
            'order_by'  => 'menu_order',
            'tax'       => '',
            'term'      => '',
            'layout'    => '',
        ), $atts) );

        if( null != $id ) {
            $id_output = 'id="' . $id . '"';
        }

        if( null == $id ) {
	        $id = 'wanna'.md5( date( 'jnYgis' ) );
	        $id_output = 'id="' . $id . '"';
	    }

        $isotope_args = array(
            'post_type'       => $type,
            'order'           => $order,
            'orderby'         => $order_by,
            'posts_per_page'  => $items
        );
        if( ! is_null( $term ) ){
            $isotope_args['tax_query'] = array(
                array(
                    'taxonomy' => $tax,
                    'field'    => 'slug',
                    'terms'    => $term,
                ),
            );
        }

        $isotope_loop = new WP_Query( $isotope_args );

        $isotope_output = '';

        if ( $isotope_loop->have_posts() ) :

            if( $tax != null && $term == null ) {
                $isotope_output .= '<ul id="filters-' . $id . '" class="filters">';
                $terms = get_terms( $tax );
                $count = count($terms);
                $isotope_output .= '<li><a href="javascript:void(0)" title="filter all" data-filter=".all" class="active">All</a></li>';
                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $termname = strtolower($term->slug);
                        $isotope_output .= '<li><a href="javascript:void(0)" title="filter ' . $term->name . '" data-filter=".' . $termname . '">' . $term->name . '</a></li>';
                    }
                }
                $isotope_output .= '</ul>';
            } elseif ( $term != null ) {
                $isotope_output .= '<ul id="filters-' . $id . '" class="filters">';
                $term_id = get_term_by( 'slug', $term, $tax );
                $terms = get_term_children( $term_id->term_id, $tax );
                $count = count($terms);
                $isotope_output .= '<li><a href="javascript:void(0)" title="filter all" data-filter=".all" class="active">All</a></li>';
                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $single_term = get_term( $term, $tax );
                        $termslug = strtolower($single_term->slug);
                        $termname = strtolower($single_term->name);
                        $isotope_output .= '<li><a href="javascript:void(0)" title="filter ' . $termslug . '" data-filter=".' . $termslug . '">' . $termname . '</a></li>';
                    }
                }
                $isotope_output .= '</ul>';
            }

            $isotope_output .= '<ul ' . $id_output . ' class="isotope-content isotope">';

            while ( $isotope_loop->have_posts() ) : $isotope_loop->the_post();
                if( has_post_thumbnail( $isotope_loop->ID ) ) {
                    $image = '<a href="' . get_the_permalink() . '" title="' . get_the_title() . '">' . get_the_post_thumbnail( $isotope_loop->ID, 'medium' ) . '</a>';
                }
                if( $tax != null ) {
                    $tax_terms = get_the_terms( $isotope_loop->ID, $tax );
                    $term_class = '';
                    foreach( (array)$tax_terms as $term ) {
                        $term_class .= $term->slug . ' ';
                    }
                }
                $isotope_output .= '<li class="isotope-item ' . $term_class . 'all">' . $image . '</li>';
                $image = '';
            endwhile;

            $isotope_output .= '</ul>';

        endif;

        wp_reset_query();

        wp_enqueue_style( $this->plugin_name );
        wp_enqueue_script( $this->plugin_name . 'isotope-init' );
        $isovars = array( 'id' => $id );
        $isovars['layoutMode'] = ( ! empty( $layout ) )? $layout : 'masonry';
        wp_localize_script( $this->plugin_name . 'isotope-init', 'isovars', $isovars );

        return $isotope_output;

	}

}