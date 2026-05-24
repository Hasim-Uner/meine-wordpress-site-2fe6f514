<?php

/**
 * Minimal Advanced Custom Fields stubs for static analysis.
 *
 * @param string $selector
 * @param int|string|false|null $post_id
 * @param bool $format_value
 * @return mixed
 */
function get_field( $selector, $post_id = false, $format_value = true ) {}

/**
 * @param string $selector
 * @param mixed $value
 * @param int|string|false|null $post_id
 * @return bool
 */
function update_field( $selector, $value, $post_id = false ) {}

/**
 * @param array<string, mixed> $field_group
 * @return void
 */
function acf_add_local_field_group( array $field_group ) {}
