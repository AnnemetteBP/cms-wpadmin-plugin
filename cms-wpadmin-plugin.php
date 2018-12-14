<?php
/*
Plugin Name:  CMS WP Customize Admin Dashboard Plugin
Description:  Admin plugin that allows you to clean up the WP dashboard.
Version:      1.0.0
Author:       Annemette Pirchert
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  cms-wpadmin-plugin
*/
// Checking the absolute/constant path is Wordpress - for security. This can also be done, by checking an if statement
defined ('ABSPATH') or die('Nothing to see here.');
//require_once ('includes/plugin.php');

//The plugin class and its methods - the class name should be unique and always begin with capital letter
class CmsWpadminPlugin
{
    //Protected (only visible to this class) array of menu items
    protected $hideMenuItems = [
        "hide-posts" => "Posts",
        "hide-media" => "Media",
        "hide-index" => "Dashboard",
        "hide-pages" => "Pages",
        "hide-comments" => "Comments",
        "hide-themes" => "Appearance",
        "hide-plugins" => "Plugins",
        "hide-users" => "Users",
        "hide-tools" => "Tools"
        //"hide-options-general" => "Settings" - not included in removable items, because plugin is located as sub-page in WP settings
    ];

    public function __construct()
    {
        // $this - refers to in this class
        // Add settings page content and functionality on class initiation - the callable declared. add action - WP function
        add_action("admin_init", array($this, "setup_settings_page"));
        // Adds the settings page as sub page to general options
        add_action("admin_menu", array($this, "add_subpage"));
        //Remove the selected menu items selected in the settings page
        add_action("admin_menu", array($this, "remove_menus"), 999);

    }

    // Add settings page content and functionality
    function setup_settings_page()
    {
        // Add settings sections in settings page
        add_settings_section(
            "menuhide",
            "Hide from menu",
            null,
            "customize-wp-backend"
        );
        //Iterating over the list of items in protected array
        foreach($this->hideMenuItems as $menuItem => $label )
        {
            add_settings_field(
                $menuItem,               //(string) (Required) Slug-name to identify the field. Used in the 'id' attribute of tags.
                $label,               //(string) (Required) Formatted title of the field. Shown as the label for the field during output.
                array($this, "hide"), //(callable) (Required) Function that fills the field with the desired form inputs. The function should echo its output.
                "customize-wp-backend",     //(string) (Required) The slug-name of the settings page on which to show the section (general, reading, writing, ...).
                "menuhide",                  //(string) (Optional) The slug-name of the section of the settings page in which to show the box.
                array(
                    $menuItem
                )
            );
            // Register settings in DB
            register_setting(
                "menuhide",
                $menuItem
            );
        }
    }


// Remove the selected menu items selected in the settings page
    function remove_menus()
    {
        foreach(wp_load_alloptions() as $option => $value)
        {
            // Hide menu items
            if (in_array($option, array_keys($this->hideMenuItems)) && $value == 1) {
                if($option === "hide-posts"){
                    remove_menu_page("edit.php");
                }
                else if($option === "hide-media"){
                    remove_menu_page( 'upload.php' );                 //Media
                }
                else if($option === "hide-index"){
                    remove_menu_page( 'index.php' );                  //Dashboard
                }
                else if($option === "hide-comments"){
                    remove_menu_page( 'edit-comments.php' );          //Comments
                }
                else if($option === "hide-pages"){
                    remove_menu_page( 'edit.php?post_type=page' );    //Pages
                }
                else if($option === "hide-themes"){
                    remove_menu_page( 'themes.php' );                 //Appearance
                }
                else if($option === "hide-plugins"){
                    remove_menu_page( 'plugins.php' );                //Plugins
                }
                else if($option === "hide-users"){
                    remove_menu_page( 'users.php' );                  //Users
                }
                else if($option === "hide-tools"){
                    remove_menu_page( 'tools.php' );                  //Tools
                }
                else if($option === "hide-options-general"){
                    remove_menu_page( 'options-general.php' );        //Settings
                }
                else if($option === "hide-jetpack"){
                    remove_menu_page( 'jetpack' );                    //Jetpack*
                }


            }
        }
    }

    // Front-end of the settings page
    function settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Customize WP Dashboard</h1>

            <!-- options.php is a Wordpress file that does most logic -->
            <form method="post" action="options.php">
                <?php
                // Insert section, fields and save button
                do_settings_sections("customize-wp-backend");
                settings_fields("menuhide");
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Adds the settings page as a sub page to the WP Settings menu item
    function add_subpage()
    {
        add_submenu_page
        (
            "options-general.php",
            "Customize WP Backend Settings",
            "Customize WP Backend",
            "manage_options",
            "customize-wp-backend",
            array($this, "settings_page")
        );
    }
// Hide menu item - checking if option is checked, by assiging the value equal to 1
    function hide($options)
    {
        // Compare the stored value with 1.
        // Stored value is 1 if user checks the checkbox otherwise empty string.
        // Will output: checked='checked' or nothing
        $isChecked = checked(1, get_option($options[0]), false);
        echo "<input type='checkbox' name='" . $options[0] . "' value='1' {$isChecked} />";
    }
}
//Initiating the class - creating the object
$cmsWpadminPlugin = new cmsWpadminPlugin();