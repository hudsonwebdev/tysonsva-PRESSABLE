<?php
namespace FileBird\Classes\Attachment;

defined( 'ABSPATH' ) || exit;

use FileBird\Model\UserSettingModel;
use FileBird\Classes\Helpers;

class AttachmentOrder {
	private $query;
	private $clauses;
	private $userSettingModel;

	public function __construct( $query, $clauses ) {
		$this->query            = $query;
		$this->clauses          = $clauses;
		$this->userSettingModel = UserSettingModel::getInstance();
	}

	public function apply_order() {
		global $wpdb;

		$meta_key     = AttachmentSize::META_KEY;
		$order        = $this->query->get( 'order' );
		$orderby      = $this->query->get( 'orderby' );
		$option_order = $this->userSettingModel->get( 'DEFAULT_SORT_FILES' );

		if ( $orderby === $meta_key && ! empty( $order ) ) {
			$this->order_by_filesize( $order );
		}

        if ( $orderby === 'fb_filename' ) {
			$this->clauses['fields'] .= ' ,SUBSTRING_INDEX(postmeta.meta_value, \'/\', -1) as postfilename ';
			$this->clauses['join']   .= " LEFT JOIN {$wpdb->postmeta} AS postmeta ON ({$wpdb->posts}.ID = postmeta.post_id AND postmeta.meta_key = '_wp_attached_file') ";
			$this->clauses['orderby'] = " CAST(postfilename AS UNSIGNED), postfilename {$order} ";
		}

		if ( Helpers::isListMode() && ! \is_null( $option_order ) && 'default' !== $option_order['orderby'] ) {
			if ( '' === $orderby ) {
				$this->apply_default_order( $option_order );
			}
		}

		return $this->clauses;
	}

	/**
	 * Apply the startup order picked in Settings, used when the request names no order of its own.
	 *
	 * @param array $option_order The stored orderby/order pair.
	 */
	private function apply_default_order( $option_order ) {
		global $wpdb;

		// File size lives in post meta rather than in a wp_posts column, so it takes the same join
		// an explicitly requested size sort does instead of the post_ prefix below.
		if ( AttachmentSize::META_KEY === $option_order['orderby'] ) {
			$this->order_by_filesize( $option_order['order'] );
			return;
		}

		if ( ! \in_array( $option_order['orderby'], array( 'title', 'date', 'modified', 'author' ), true ) ) {
			return;
		}

		$order = $this->normalize_order( $option_order['order'] );

		$this->clauses['orderby'] = "{$wpdb->posts}.post_{$option_order['orderby']} {$order}";
	}

	/**
	 * Order by the file size meta, which has to be compared as a number rather than as the string
	 * it is stored as.
	 *
	 * @param string $order ASC or DESC.
	 */
	private function order_by_filesize( $order ) {
		global $wpdb;

		$meta_key = AttachmentSize::META_KEY;
		$order    = $this->normalize_order( $order );

		$this->clauses['join']   .= " LEFT JOIN {$wpdb->postmeta} AS fbmt ON ({$wpdb->posts}.ID = fbmt.post_id AND fbmt.meta_key = '{$meta_key}') ";
		$this->clauses['orderby'] = " fbmt.meta_value + 0 {$order} ";
	}

	/**
	 * Both the request and the stored setting reach these clauses as free text, so the direction is
	 * reduced to one of the two keywords before it is interpolated.
	 *
	 * @param string $order The requested direction.
	 * @return string
	 */
	private function normalize_order( $order ) {
		return 'DESC' === \strtoupper( (string) $order ) ? 'DESC' : 'ASC';
	}
}