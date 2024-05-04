<?php 

/*
 * Plugin Name:       Shortcode Plugin
 * Plugin URI:        https://trongnhandev.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Trong Nhan
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       shortcode-plugin
 * Domain Path:       /languages
 */

defined("ABSPATH") or die("You can not access directly");
define("PLUGIN_PATH", plugin_dir_path( __FILE__ ));
define("PLUGIN_URI", plugin_dir_url( __FILE__ ));
//admin_menu hook
// Design patterns -> SingleTon
if(!class_exists('ShortcodePlugin')) {
  class ShortcodePlugin {
  public function __construct() {
   add_action('wp_enqueue_scripts', array($this, 'load_assets'));
   add_action( 'admin_menu', array($this, 'custom_admin_menu') );
   add_shortcode('shortcode_authors', array($this, 'render_authors'));
   add_shortcode('authors_by_name', array($this, 'render_author_by_name'));
  }

  function custom_admin_menu() {
    add_menu_page( 'Shortcode Author', 'Shortcode Author', 'manage_options', 'shortcode-authors', array($this, 'render_shortcode'), '', 10 );
    add_submenu_page( 'shortcode-authors', 'Short code Attribute', 'Shortcode Attr', 'manage_options', 'shortcode-attr', array($this, 'render_attr_shortcode') );
  }
  //List Employees
  function render_shortcode() {
    ?>
      <h3 class="text-center">Shortcode Display Top 3 Author with 1 latest post</h3>
      <p>Shortcode: [shortcode_authors]</p>
    <?php 
  }

  function render_attr_shortcode () {
    ?>
      <h3 class="text-center">Shortcode Display Top 3 Author with 1 latest post</h3>
      <p>Shortcode: [authors_by_name] có thể gán thuộc tính cho shortcode </p>
      <span>Example: [authors_by_name name='a']</span>
    <?php
  }

  //Render shortcode
  function render_authors() {
    global $wpdb; 
    $results =  $wpdb->get_results($wpdb->prepare("select post_author, ID, post_title from wp_posts where ID in 
    ( SELECT max(ID) as postid FROM wp_posts 
    WHERE post_type = 'post' and post_status = 'publish' 
    GROUP BY post_author ORDER BY postid DESC) limit 0,3;",""),ARRAY_A);
    $html = '';
    $html .= "<div class='container'>";
    $html .= "<div class='row'>";
    foreach ($results as $item) {
       $html .= "<div class='col-4'>";
       $html .= "<div class='card'>";
       $html .= "<img src=". get_avatar_url( $item['post_author'] ) . "class='card-img-top'  alt='...'>";
       $html .= "<div class='card-body'>";
       $html .= "<h5 class='card-title'>" . get_author_name( $item['post_author'] ) . "</h5>";
       $html .= "<p class='card-text'>" . $item['post_title'] . "</p>";
       $html .= "</div>";
       $html .= "</div>";
       $html .= "</div>";
      
    }
    $html .= "</div>";
    $html .= "</div>";

    return $html;


  }


  function render_author_by_name($attr) {
    global $wpdb;

    $name = $attr['name'];
    $authors = $wpdb->get_results("select * from wp_users where user_nicename LIKE '%$name%'", ARRAY_A);

    $html = '';
    $html .= "<div class='container'>";
    $html .= "<div class='row'>";
      foreach ($authors as $item) {
        $html .= "<div class='col-4'>";
        $html .= "<div class='card'>";
        $html .= "<img src=" . get_avatar_url($item['ID']) . "class='card-img-top'  alt='...'>";
        $html .= "<div class='card-body'>";
        $html .= "<h5 class='card-title'>" . get_author_name($item['ID']) . "</h5>";
        $html .= "<p class='card-text'>" . $item['user_email'] . "</p>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

    }
        $html .= "</div>";
        $html .= "</div>";

        return $html;



  }

  

  //Nhúng thư viện js và css vào trong plugin 
  function load_assets() {
    //Nhúng thư viện css
    wp_enqueue_style( 'bootstrap_min_css', PLUGIN_URI."css/bootstrap.min.css", array(), '1.0.0', 'all' );
    //Nhúng thư viện js
    wp_enqueue_script( 'bootstrap_min_js', PLUGIN_URI."js/bootstrap.min.js", array('jquery'), '1.0.0', true );
  }
}
}
$plugins = new ShortcodePlugin();





