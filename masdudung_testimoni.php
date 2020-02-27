<?php
/*
Plugin Name: Masdudung Testimonial Plugin
Plugin URI: https://jadipesan.com
Description: Simple non-bloated WordPress Contact Form
Version: 1.0
Author: masdudung
Author URI: https://jadipesan.com
*/


class masdudung_testimoni
{
    public function MD_testimoni_form() 
    {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Your Name (required) <br/>';
        echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Email (required) <br/>';
        echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Phone (required) <br/>';
        echo '<input type="text" name="cf-phone" pattern="[0-9 ]+" value="' . ( isset( $_POST["cf-phone"] ) ? esc_attr( $_POST["cf-phone"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Testimonial (required) <br/>';
        echo '<textarea rows="10" cols="35" name="cf-testimonial">' . ( isset( $_POST["cf-testimonial"] ) ? esc_attr( $_POST["cf-testimonial"] ) : '' ) . '</textarea>';
        echo '</p>';
        echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
        echo '</form>';
    }


    public function MD_testimoni_save() {
        global $wpdb;
        global $blog_id;

        // if the submit button is clicked, send the email
        if ( isset( $_POST['cf-submitted'] ) ) {

            // sanitize form values
            $data = array();
            $data['name']    = sanitize_text_field( $_POST["cf-name"] );
            $data['email']   = sanitize_email( $_POST["cf-email"] );
            $data['phone'] = sanitize_text_field( $_POST["cf-phone"] );
            $data['testimonial'] = esc_textarea( $_POST["cf-testimonial"] );

            $wpdb->insert( 
                'testimoni', 
                array(
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'testimonial' => $data['testimonial'],
                    'blog_id' => $blog_id,
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                )
            );
            if ( $data ) {
                echo '<div>';
                echo '<p>Thanks for contacting me, expect a response soon.</p>';
                echo '</div>';
            } else {
                echo 'An unexpected error occurred';
            }
        }
    }

    public function MD_testimoni_shortcode() {
        ob_start();
        $this->MD_testimoni_save();
        $this->MD_testimoni_form();
        return ob_get_clean();
    }

    function MD_testimonial_list(){
        global $wpdb;
        global $blog_id;
    
        echo '<div class="wrap">';
        echo '<h2>List Of Testimonial</h2>';
        echo '</div>';
        echo "<hr>";
        
    
        if ( isset( $_POST['cf-deleted'] ) ) 
		{
            $id = $_POST["cf-id"];
            
            $wpdb->delete( 
                'testimoni', 
                array( 'id' => $id, 'blog_id'=>$blog_id ), 
                array( '%d', '%d' ) );
            echo "Data telah terhapus";
			
        }
        
        $testimonial = $wpdb->get_results( 
            "
            SELECT * 
            FROM testimoni
            WHERE blog_id = $blog_id",
            OBJECT
        );
        
        //draw table
        echo '
        
        <table class="table table-striped">
            <thead>
                <tr>
                <th scope="col">No</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Testimonial</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
        ';
    
        $no = 1;
        foreach ($testimonial as $key => $row) {
            # code...
            echo '<tr>';
            echo '<th scope="row">'.$no.'</th>';
            echo "<td>$row->name</td>";
            echo "<td>$row->email</td>";
            echo "<td>$row->phone</td>";
            echo "<td>$row->testimonial</td>";
            echo '<td>
                <form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
                <input type="hidden" name="cf-id" value="'.$row->id.'">
                <input type="submit" value="delete" class="btn btn-danger" name="cf-deleted">
                </form>
            <td>';
            echo '</tr>';
          $no++;
            
        }
    
        echo '
            </tbody>
            </table>
        ';
    
    }
    
    function MD_testimonial_menu() {
        add_menu_page( 'MD Testimonial', 'Testimonial', 'manage_options', 'MD_testimonial.php', [$this,'MD_testimonial_list'], 'dashicons-tickets', 6  );
    }
    
}

$MD = new masdudung_testimoni();
add_shortcode( 'sitepoint_contact_form', [$MD, 'MD_testimoni_shortcode'] );
add_action( 'admin_menu', [$MD, 'MD_testimonial_menu'] );



class masdudung_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // ID widget
        'masdudung_widget',
        // nama widget
        __('Contoh Widget Masdudung', ' masdudung_widget_testimoni'),
        // deskripsi widget
        array( 'description' => __( 'Coba widget masdudung', 'masdudung_widget_testimoni' ), )
        );
    }

    public function get_random_testimoni(){
        global $wpdb;

        $testimoni = $wpdb->get_results( 
            "
            SELECT * FROM testimoni
            WHERE blog_id = $blog_id
            ORDER BY RAND()
            LIMIT 1;
            ",
            OBJECT
        );
        return $testimoni;
    }
    

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $args['before_widget'];
        //if title is present
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];
        
        //output
        $testimoni = $this->get_random_testimoni();
        var_dump($testimoni[0]);

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
        $title = $instance[ 'title' ];
        else
        $title = __( 'Masdudung', 'masdudung_widget_testimoni' );
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }

}

function masdudung_register_widget() {
    register_widget( 'masdudung_widget' );
}

add_action( 'widgets_init', 'masdudung_register_widget' );
?>