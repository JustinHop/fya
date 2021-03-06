<?php

/*
 * Copyright 2010 Matt Wiebe.
 *
 * This code is licensed under the GPL v2.0
 * http://www.opensource.org/licenses/gpl-2.0.php
 *
 * If you do something cool with it, let me know! http://somadesign.ca/contact/
 * 
 * Version 1.2
 * 
 * === Changelog ===
 * 
 * 1.0
 *  - Initial release
 * 1.1
 *  - Added feed support in URL rewrites
 * 1.2
 *  - Removed redundant post_class code.
 *  - Removed redundant single post_type template code
 *  - Introduced directory support for template files
 *
 */

/**
 * SD_Register_Post_Type class
 *
 * @author Matt Wiebe
 * @link http://somadesign.ca
 * 
 * @param string $post_type The post type to register
 * @param array $args The arguments to pass into @link register_post_type(). Some defaults provided to ensure the UI is available.
 * @param string $custom_plural The plural name to be used in rewriting (http://yourdomain.com/custom_plural/ ). If left off, an "s" will be appended to your post type, which will break some words. (person, box, ox. Oh, English.)
 **/

if ( ! class_exists('SD_Register_Post_Type') ) {

    class SD_Register_Post_Type {

        private $post_type;
        private $post_slug;
        private $args;

        private $defaults = array(
            'show_ui' => true,
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail')
        );

        public function __construct( $post_type = null, $args=array(), $custom_plural = false ) {
            if ( $post_type ) {
                $this->post_type = $post_type;
                $this->args = wp_parse_args($args, $this->defaults);
                // Uppercase the post type for label if there isn't one
                if ( ! $this->args['label'] ) {
                    $this->args['label'] = ucwords($post_type);
                }
                $this->post_slug = ( $custom_plural ) ? $custom_plural : $post_type . 's';
                
                $this->defaults['permalink_epmask'] = $this->post_slug;

                $this->add_actions();
                $this->add_filters();
            }
        }
        
        public function add_actions() {
            add_action( 'init', array($this, 'register_post_type'));
            add_action('template_redirect', array($this, 'context_fixer') );
        }

        public function add_filters() {

            add_filter( 'generate_rewrite_rules', array($this, 'add_rewrite_rules') );
            add_filter( 'template_include', array($this, 'template_include') );
            add_filter( 'body_class', array($this, 'body_classes') );
        }
        
        public function context_fixer() {
            if ( get_query_var( 'post_type' ) == $this->post_type ) {
                global $wp_query;
                $wp_query->is_home = false;
            }
        }

        public function add_rewrite_rules( $wp_rewrite ) {
            $new_rules = array();
            $new_rules[$this->post_slug . '/page/?([0-9]{1,})/?$'] = 'index.php?post_type=' . $this->post_type . '&paged=' . $wp_rewrite->preg_index(1);
            $new_rules[$this->post_slug . '/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type=' . $this->post_type . '&feed=' . $wp_rewrite->preg_index(1);
            $new_rules[$this->post_slug . '/?$'] = 'index.php?post_type=' . $this->post_type;

            $wp_rewrite->rules = array_merge($new_rules, $wp_rewrite->rules);
            return $wp_rewrite;
        }

        public function register_post_type() {
            register_post_type( $this->post_type, $this->args );        
        }

        public function template_include( $template ) {
            if ( get_query_var('post_type') == $this->post_type ) {
                
                if ( is_single() ) {
                    if ( $single = locate_template( array( $this->post_type.'/single.php') ) )
                        return $single;
                }
                else { // loop
                    return locate_template( array(
                        $this->post_type . '/index.php',
                        $this->post_type . '.php', 
                        'index.php' 
                    ));
                }

            }
            return $template;
        }

        public function body_classes( $c ) {
            if ( get_query_var('post_type') === $this->post_type ) {
                $c[] = $this->post_type;
                $c[] = 'type-' . $this->post_type;
            }
            return $c;
        }


    } // end SD_Register_Post_Type class
    
    /**
     * A helper function for the SD_Register_Post_Type class. Because typing "new" is hard.
     *
     * @author Matt Wiebe
     * @link http://somadesign.ca
     * 
     * @uses SD_Register_Post_Type class
     * @param string $post_type The post type to register
     * @param array $args The arguments to pass into @link register_post_type(). Some defaults provided to ensure the UI is available.
     * @param string $custom_plural The plural name to be used in rewriting (http://yourdomain.com/custom_plural/ ). If left off, an "s" will be appended to your post type, which will break some words. (person, box, ox. Oh, English.)
     **/

    if ( ! function_exists( 'sd_register_post_type' ) && class_exists( 'SD_Register_Post_Type' ) ) {
        function sd_register_post_type( $post_type = null, $args=array(), $custom_plural = false ) {
            $custom_post = new SD_Register_Post_Type( $post_type, $args, $custom_plural );
        }
    }

}

?>
