<?php
/**
 * NEXUS Growth Manager (Backend UI)
 * Fügt Felder zur Benutzer-Verwaltung hinzu, um das Portal zu steuern.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Felder anzeigen
function hu_show_nexus_profile_fields( $user ) {
    ?>
    <h3>NEXUS Growth Einstellungen</h3>
    <table class="form-table">
        <tr>
            <th><label for="nexus_points_total">Gesamt-Punkte (Budget)</label></th>
            <td>
                <input type="number" name="nexus_points_total" id="nexus_points_total" value="<?php echo esc_attr( get_the_author_meta( 'nexus_points_total', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Z.B. 40, 50 oder 100.</span>
            </td>
        </tr>
        <tr>
            <th><label for="nexus_points_used">Verbrauchte Punkte</label></th>
            <td>
                <input type="number" name="nexus_points_used" id="nexus_points_used" value="<?php echo esc_attr( get_the_author_meta( 'nexus_points_used', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Wie viele Punkte wurden diesen Monat schon verarbeitet?</span>
            </td>
        </tr>
        <tr>
            <th><label for="nexus_retainer_label">Retainer / Paket Name</label></th>
            <td>
                <input type="text" name="nexus_retainer_label" id="nexus_retainer_label" value="<?php echo esc_attr( get_the_author_meta( 'nexus_retainer_label', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Z.B. "Growth Retainer L" oder "Performance Audit".</span>
            </td>
        </tr>
        <tr>
            <th><label for="nexus_current_current_focus">Aktueller Fokus</label></th>
            <td>
                <input type="text" name="nexus_current_current_focus" id="nexus_current_current_focus" value="<?php echo esc_attr( get_the_author_meta( 'nexus_current_current_focus', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Was sehen wir gerade im Dashboard? Z.B. "Checkout Optimierung".</span>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'hu_show_nexus_profile_fields' );
add_action( 'edit_user_profile', 'hu_show_nexus_profile_fields' );

// 2. Felder speichern
function hu_save_nexus_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) { return false; }

    if ( isset( $_POST['nexus_points_total'] ) ) {
        update_user_meta( $user_id, 'nexus_points_total', intval( $_POST['nexus_points_total'] ) );
    }

    if ( isset( $_POST['nexus_points_used'] ) ) {
        update_user_meta( $user_id, 'nexus_points_used', intval( $_POST['nexus_points_used'] ) );
    }

    if ( isset( $_POST['nexus_retainer_label'] ) ) {
        update_user_meta( $user_id, 'nexus_retainer_label', sanitize_text_field( $_POST['nexus_retainer_label'] ) );
    }

    if ( isset( $_POST['nexus_current_current_focus'] ) ) {
        update_user_meta( $user_id, 'nexus_current_current_focus', sanitize_text_field( $_POST['nexus_current_current_focus'] ) );
    }
}
add_action( 'personal_options_update', 'hu_save_nexus_profile_fields' );
add_action( 'edit_user_profile_update', 'hu_save_nexus_profile_fields' );
