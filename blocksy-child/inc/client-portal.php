<?php
/**
 * NEXUS Client Portal
 * Shortcode: [hu_performance_cockpit]
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function hu_render_performance_cockpit() {
    if ( ! is_user_logged_in() ) {
        return hu_render_custom_login_form();
    }

    $current_user = wp_get_current_user();
    $portal_meta  = get_user_meta( get_current_user_id(), 'nexus_client_portal', true );
    $portal_meta  = is_array( $portal_meta ) ? $portal_meta : [];
    $retainer     = is_array( $portal_meta['retainer'] ?? null ) ? $portal_meta['retainer'] : [];
    $kpis         = is_array( $portal_meta['kpis'] ?? null ) ? array_filter( $portal_meta['kpis'], 'is_array' ) : [];
    $roadmap      = is_array( $portal_meta['roadmap'] ?? null ) ? array_filter( $portal_meta['roadmap'], 'is_array' ) : [];

    $portal_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
    $total_units = max( 0, (float) ( $retainer['total'] ?? 0 ) );
    $used_units  = max( 0, (float) ( $retainer['used'] ?? 0 ) );
    $percent     = $total_units > 0 ? min( 100, ( $used_units / $total_units ) * 100 ) : 0;
    $has_retainer_data = $total_units > 0 || $used_units > 0 || ! empty( $retainer['label'] ) || ! empty( $retainer['focus'] );

    $upload_notice = '';
    $upload_notice_type = 'notice';

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['nexus_upload_nonce'] ) ) {
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nexus_upload_nonce'] ) ), 'nexus_upload' ) ) {
            $upload_notice = 'Upload fehlgeschlagen: Sicherheitsprüfung.';
            $upload_notice_type = 'error';
        } elseif ( ! current_user_can( 'upload_files' ) ) {
            $upload_notice = 'Upload fehlgeschlagen: Keine Berechtigung.';
            $upload_notice_type = 'error';
        } elseif ( empty( $_FILES['nexus_upload_file'] ) || ! isset( $_FILES['nexus_upload_file']['error'] ) ) {
            $upload_notice = 'Upload fehlgeschlagen: Datei fehlt.';
            $upload_notice_type = 'error';
        } else {
            $file = $_FILES['nexus_upload_file'];
            $max_size = 50 * 1024 * 1024;
            $allowed_ext = [ 'pdf', 'jpg', 'jpeg', 'png', 'mp4', 'mov', 'webm' ];
            $filetype = wp_check_filetype( $file['name'] );

            if ( $file['error'] !== UPLOAD_ERR_OK ) {
                $upload_notice = 'Upload fehlgeschlagen: Fehler beim Hochladen.';
                $upload_notice_type = 'error';
            } elseif ( $file['size'] > $max_size ) {
                $upload_notice = 'Upload fehlgeschlagen: Datei größer als 50 MB.';
                $upload_notice_type = 'error';
            } elseif ( empty( $filetype['ext'] ) || ! in_array( strtolower( $filetype['ext'] ), $allowed_ext, true ) ) {
                $upload_notice = 'Upload fehlgeschlagen: Dateityp nicht erlaubt.';
                $upload_notice_type = 'error';
            } else {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                $upload = wp_handle_upload( $file, [ 'test_form' => false ] );

                if ( isset( $upload['error'] ) ) {
                    $upload_notice = 'Upload fehlgeschlagen: ' . $upload['error'];
                    $upload_notice_type = 'error';
                } else {
                    $attachment = [
                        'guid'           => $upload['url'],
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) ),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                        'post_author'    => get_current_user_id(),
                    ];
                    $attach_id = wp_insert_attachment( $attachment, $upload['file'] );

                    if ( $attach_id ) {
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        $upload_notice = 'Upload erfolgreich.';
                        $upload_notice_type = 'success';
                    } else {
                        $upload_notice = 'Upload fehlgeschlagen: Anhang konnte nicht erstellt werden.';
                        $upload_notice_type = 'error';
                    }
                }
            }
        }
    }

    ob_start();
    ?>
    <div class="nexus-dashboard">
        <header class="nd-header">
            <div class="nd-welcome">
                <span class="nd-badge">Insight Hub</span>
                <h2>Moin, <?php echo esc_html( $portal_name ); ?></h2>
            </div>
            <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-ghost btn-sm">Logout</a>
        </header>

        <div class="nd-grid">
            <div class="nd-card span-2">
                <h3>Ressourcen</h3>
                <?php if ( $has_retainer_data ) : ?>
                    <?php if ( ! empty( $retainer['label'] ) ) : ?>
                        <p class="muted"><?php echo esc_html( (string) $retainer['label'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $total_units > 0 ) : ?>
                        <div class="nd-progress-wrap">
                            <div class="nd-progress-bar" style="width:<?php echo esc_attr( $percent ); ?>%"></div>
                        </div>
                        <div class="nd-stats">
                            <span><?php echo esc_html( number_format_i18n( $used_units, 1 ) ); ?> / <?php echo esc_html( number_format_i18n( $total_units, 1 ) ); ?> Pkt</span>
                            <?php if ( ! empty( $retainer['focus'] ) ) : ?>
                                <span class="muted"><?php echo esc_html( (string) $retainer['focus'] ); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php elseif ( ! empty( $retainer['focus'] ) ) : ?>
                        <p class="muted"><?php echo esc_html( (string) $retainer['focus'] ); ?></p>
                    <?php else : ?>
                        <p class="muted">Noch keine Punktplanung hinterlegt.</p>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="muted">Noch keine Ressourcenplanung hinterlegt.</p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $kpis ) ) : ?>
                <?php foreach ( $kpis as $k ) : ?>
                    <div class="nd-card kpi-card">
                        <span class="muted"><?php echo esc_html( (string) ( $k['label'] ?? 'Kennzahl' ) ); ?></span>
                        <strong class="kpi-val"><?php echo esc_html( (string) ( $k['value'] ?? '—' ) ); ?></strong>
                        <?php if ( ! empty( $k['trend'] ) ) : ?>
                            <span class="muted"><?php echo esc_html( (string) $k['trend'] ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="nd-card">
                    <h3>Kennzahlen</h3>
                    <p class="muted">Noch keine KPI-Daten hinterlegt.</p>
                </div>
            <?php endif; ?>

            <div class="nd-card span-full">
                <h3>Roadmap</h3>
                <?php if ( ! empty( $roadmap ) ) : ?>
                    <?php foreach ( $roadmap as $r ) : ?>
                        <div class="nd-item status-<?php echo esc_attr( sanitize_html_class( (string) ( $r['status'] ?? 'open' ) ) ); ?>">
                            <span class="dot"></span>
                            <span><?php echo esc_html( (string) ( $r['task'] ?? 'Offener Punkt' ) ); ?></span>
                            <small class="muted"><?php echo esc_html( (string) ( $r['impact'] ?? '' ) ); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="muted">Noch keine Roadmap-Punkte hinterlegt.</p>
                <?php endif; ?>
            </div>
            <div class="nd-card span-full">
                <h3>Uploads</h3>
                <?php if ( $upload_notice ) : ?>
                    <div class="nd-upload-note <?php echo esc_attr( $upload_notice_type ); ?>">
                        <?php echo esc_html( $upload_notice ); ?>
                    </div>
                <?php endif; ?>
                <form class="nd-upload-form" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'nexus_upload', 'nexus_upload_nonce' ); ?>
                    <input type="file" name="nexus_upload_file" accept=".pdf,.jpg,.jpeg,.png,.mp4,.mov,.webm" required>
                    <button type="submit" class="btn btn-primary">Datei hochladen</button>
                    <p class="muted">Erlaubt: PDF, JPG/PNG, MP4/MOV/WEBM. Max 50 MB.</p>
                </form>
                <?php
                $uploads = get_posts( [
                    'post_type'      => 'attachment',
                    'posts_per_page' => 10,
                    'author'         => get_current_user_id(),
                    'post_status'    => 'inherit',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ] );
                ?>
                <?php if ( $uploads ) : ?>
                    <div class="nd-upload-list">
                        <?php foreach ( $uploads as $upload_item ) : ?>
                            <div class="nd-upload-item">
                                <a href="<?php echo esc_url( wp_get_attachment_url( $upload_item->ID ) ); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html( get_the_title( $upload_item->ID ) ); ?>
                                </a>
                                <span class="muted"><?php echo esc_html( get_the_date( '', $upload_item->ID ) ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="muted">Noch keine Uploads vorhanden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'hu_performance_cockpit', 'hu_render_performance_cockpit' );

function nexus_render_client_portal_profile_fields( $user ) {
    if ( ! ( $user instanceof WP_User ) || ! current_user_can( 'edit_user', $user->ID ) ) {
        return;
    }

    $portal_meta = get_user_meta( (int) $user->ID, 'nexus_client_portal', true );
    $portal_json = is_array( $portal_meta )
        ? wp_json_encode( $portal_meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        : '';
    ?>
    <h2>Client Portal</h2>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="nexus-client-portal-json">Portal-Daten</label></th>
            <td>
                <textarea
                    id="nexus-client-portal-json"
                    name="nexus_client_portal_json"
                    rows="14"
                    class="large-text code"
                    placeholder='{"retainer":{"label":"Projekt","total":40,"used":12,"focus":"Tracking"},"kpis":[{"label":"Leads","value":"12","trend":"+3"}],"roadmap":[{"status":"active","task":"Tracking prüfen","impact":"Datenbasis"}]}'
                ><?php echo esc_textarea( (string) $portal_json ); ?></textarea>
                <p class="description">
                    Optionales JSON für Ressourcen, KPI-Karten und Roadmap im Client Portal. Leer lassen, wenn das Portal nur Login, Uploads und Empty-States zeigen soll.
                </p>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'nexus_render_client_portal_profile_fields' );
add_action( 'edit_user_profile', 'nexus_render_client_portal_profile_fields' );

function nexus_validate_client_portal_profile_fields( $errors, $update, $user ) {
    if ( ! isset( $_POST['nexus_client_portal_json'] ) || ! ( $user instanceof WP_User ) || ! current_user_can( 'edit_user', $user->ID ) ) {
        return;
    }

    $raw = trim( (string) wp_unslash( $_POST['nexus_client_portal_json'] ) );

    if ( '' === $raw ) {
        return;
    }

    $decoded = json_decode( $raw, true );

    if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
        $errors->add( 'nexus_client_portal_json_invalid', 'Client-Portal-Daten müssen gültiges JSON-Objekt oder leer sein.' );
    }
}
add_action( 'user_profile_update_errors', 'nexus_validate_client_portal_profile_fields', 10, 3 );

function nexus_save_client_portal_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['nexus_client_portal_json'] ) ) {
        return;
    }

    $raw = trim( (string) wp_unslash( $_POST['nexus_client_portal_json'] ) );

    if ( '' === $raw ) {
        delete_user_meta( $user_id, 'nexus_client_portal' );
        return;
    }

    $decoded = json_decode( $raw, true );

    if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
        update_user_meta( $user_id, 'nexus_client_portal', $decoded );
    }
}
add_action( 'personal_options_update', 'nexus_save_client_portal_profile_fields' );
add_action( 'edit_user_profile_update', 'nexus_save_client_portal_profile_fields' );

function hu_render_custom_login_form() {
    $form = wp_login_form(
        [
            'echo'           => false,
            'redirect'       => get_permalink(),
            'form_id'        => 'nexus-login-form',
            'label_username' => 'E-Mail oder Benutzername',
            'label_password' => 'Passwort',
            'label_remember' => 'Angemeldet bleiben',
            'label_log_in'   => 'Login',
        ]
    );

    ob_start();
    ?>
    <section class="nexus-login">
        <div class="nexus-login-card">
            <span class="nexus-login-badge">Client Access</span>
            <h2>Performance Cockpit</h2>
            <p class="nexus-login-subtitle">Bitte einloggen, um Ihr Dashboard zu sehen.</p>
            <?php echo $form; // raw-ok -- wp_login_form() markup ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['webm'] = 'video/webm';
    $mimes['mov'] = 'video/quicktime';
    return $mimes;
} );
