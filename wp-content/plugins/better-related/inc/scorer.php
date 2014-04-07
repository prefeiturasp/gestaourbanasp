<?php

/**
 * @package better-related
 * @subpackage scorer
 * @since 0.1
 */
class BetterRelatedScorer extends BetterRelated {

	/**
	 * Temporarily hold the related score of one or many posts
	 *
	 * @since 0.0.1
	 *
	 * @var array postid => array of related posts => score of single post
	 */
	private $score = array();

	/**
	 * Constructor, set up the score variable
	 *
	 * @since 0.0.1
	 *
	 * @return none
	 */
	public function __construct( $config = null ) {
		BetterRelated::__construct();
		if ( isset( $config ) )
			$this->override_options( $config );
	}

	/**
	 * Return the score of related posts
	 *
	 * Generate the score if necessary and store it using one of the storage
	 * backends.
	 *
	 * @todo check post meta for manual relatedness scores
	 * @since 0.0.1
	 *
	 * @param int $id Post ID
	 * @return array Posts IDs and their relatedness score
	 */
	public function get_score_for_post( $id = null ) {
		switch ( $this->get_option( 'storage' ) ) {
			case 'transient':
				$score = get_transient( $this->get_option( 'storage_id' ) . $id );
			break;
			default:
			case 'postmeta':
				$score = get_post_meta(
					$id,
					$this->get_option( 'storage_id' ),
					true
				);
			break;
		}
		$offset = 0;
		if ( $score ) {
			$offset = $this->get_offset( $score );
			if ( $score['ctime'] >= $this->get_option( 'mtime' ) && !$offset ) {
				return $score;
			}
			elseif( !$offset ) {
				$this->log( "stale score for post $id", 'storage' );
			}
			else{
				$this->log( "preserve old score, offset $offset", 'storage' );
				$this->score = $score;
			}
		}
		$this->build_score_for_post( $id, $offset );
		$this->save_score_for_post( $id );
		return $this->score;
	}

	/**
	 * Find related posts for a string.
	 *
	 * This is intended to be used instead of the built-in WordPress search.
	 *
	 * @since 0.4.2
	 *
	 * @return array A score array
	 */
	public function get_score_for_string( $string, $do_s2t = 1, $do_s2c = 1 ) {
		if ( $weight = $do_s2t ) {
			$this->score_by_content(
				0,
				$string,
				'post_title',
				$weight,
				0
			);
		}
		if ( $weight = $do_s2c ) {
			$this->score_by_content(
				0,
				$string,
				'post_content',
				$weight,
				0
			);
		}
		return $this->score;
	}

	/**
	 * Calculate the offset for the new query
	 *
	 * @since 0.3.5
	 *
	 * @param array $score a score set
	 * @return int offset
	 */
	private function get_offset( $score ) {
		if ( $this->get_option( 'incremental' ) ) {
			$offset = $score['offset'];
			if ( $offset < $this->get_option( 't_querylimit' ) ) {
				$offset += $this->get_option( 'querylimit' );
				return $offset;
			}
		}
		return 0;
	}

	/**
	 * Save the score for a post
	 *
	 * @since 0.2.7
	 *
	 * @param integer $id post id
	 * @return set_transient success
	 */
	private function save_score_for_post( $id ) {
		$this->timestamp_score();
		$storage = $this->get_option( 'storage' );
		switch ( $storage ) {
			case 'transient':
				$success = set_transient(
					$this->get_option( 'storage_id' ) . $id,
					$this->score,
					$this->get_option( 'cachetime' )
				);
			break;
			default:
			case 'postmeta':
				$success = update_post_meta(
					$id,
					$this->get_option( 'storage_id' ),
					$this->score
				);
			break;
		}
		$this->log( "saved score for $id ($storage): $success", 'storage' );
	}

	/**
	 * Build the score list for a single post
	 *
	 * @since 0.0.1
	 *
	 * @param integer $id post id
	 * @return none
	 */
	private function build_score_for_post( $id, $offset = 0 ) {
		if ( !$id ) // check why this gets called twice, and $id would be empty
			return;
		$this->score['offset']	= $offset;
		$this->score['stime']	= $this->microtime( true );
		$this->score['queries']	= 0;
		$this->build_score_for_content( $id, $offset );
		if ( $weight = $this->get_option( 'do_x2x' ) )
			$this->build_score_for_taxonomies( $id, $weight, $offset );
		$this->score['etime'] = $this->microtime( true );
	}

	/**
	 * Build the score for a post's content
	 *
	 * This uses various methods which can all be enabled/disabled.
	 *
	 * @since 0.2
	 *
	 * @param integer $id Post ID
	 * @return none
	 */
	private function build_score_for_content( $id, $offset ) {
		// @todo only if k2c || k2t
		$content  = get_the_content( $id );
		$title    = get_the_title( $id );
		$keywords = $this->get_keywords( $id );
		if ( $weight = $this->get_option( 'do_t2t' ) )
			$this->score_by_content(
				$id,
				$title,
				'post_title',
				$weight,
				$offset
			);
		if ( $weight = $this->get_option( 'do_t2c' ) )
			$this->score_by_content(
				$id,
				$title,
				'post_content',
				$weight,
				$offset
			);
		if ( $weight = $this->get_option( 'do_c2c' ) )
			$this->score_by_content(
				$id,
				$content,
				'post_content',
				$weight,
				$offset
			);
		if ( is_array( $keywords ) ) {
			$keywords = implode( ' ', $keywords );
			if ( $weight = $this->get_option( 'do_k2c' ) )
				$this->score_by_content(
					$id,
					$keywords,
					'post_content',
					$weight,
					$offset
				);
			if ( $weight = $this->get_option( 'do_k2t' ) )
				$this->score_by_content(
					$id,
					$keywords,
					'post_title',
					$weight,
					$offset
				);
		}
	}

	/**
	 * Return all terms of a post. Use all public taxonomies,
	 *
	 * @since 0.2.7
	 *
	 * @param integer $id post ID
	 * @return array of terms
	 */
	private function get_keywords( $id ) {
		// Get category terms
		if ( $cats = get_the_category( $id ) )
			foreach( $cats as $c )
				$keywords[] = $c->cat_name;
		// Get tags terms
		if ( $tags = get_the_tags( $id ) )
			foreach ( $tags as $tag )
				$keywords[] = $tag->name;
		// Get terms of all custom taxonomies
		$taxonomies	= get_taxonomies( array(
			'public'	=> true,
			'_builtin'	=> false
		) );
		if  ( $taxonomies )
			foreach ( $taxonomies as $taxonomy )
				if ( $terms = get_the_terms( $id, $taxonomy ) )
					foreach ( $terms as $term )
						$keywords[] = $term->name;
		return $keywords;
	}

	/**
	 * Build a list of related posts by content and award a score
	 *
	 * This uses the mysql relatedness score for fulltexts, see
	 * http://dev.mysql.com/doc/refman/5.0/en/fulltext-search.html . We score
	 * against post_content at the moment, which can include HTML markup. This
	 * could probably be improved but would increase disk space consumption
	 * because either the_content_filtered or a new column/table would be
	 * necessary.
	 *
	 * @since 0.2
	 * @todo limit by date
	 * @todo multisite -> related post on a network
	 * @todo search topic + content at once (?)
	 *
	 * @param int $id Post ID
	 * @param string $string String to search for in other post's content
	 * @param string $column DB column to search
	 * @param float $weight Scoring weight
	 * @param int offset Query offset
	 * @return none
	 */
	private function score_by_content( $id, $string, $column = 'post_content', $weight, $offset ) {
		global $wpdb;
		$prefix	= $wpdb->prefix;
		$string	= $wpdb->escape( $string );
		// don't care about post types @todo update the docs, say that different
		// post types don't necessarily require different storage
		$query = "
		SELECT ID,
		MATCH ($column) AGAINST ('$string')
		FROM {$prefix}posts
		WHERE ( post_status='publish' OR post_status = 'private' )
		AND ID != $id ";
		// @todo Wouldn't it be better to find all post types and do the
		// filtering in the display?
		if ( $this->get_option( 'usept' ) )
			$post_type = array_keys( $this->get_option( 'usept' ) );
		if ( is_string( $post_type ) ) {
			$query .= " AND {$prefix}posts.post_type = '$post_type' ";
		}
		elseif( is_array( $post_type ) ) {
			$query .= " AND ( ";
			$multiple = false;
			foreach( $post_type as $type ) {
				if ( $multiple )
					$query .= ' OR ';
				$query .= " {$prefix}posts.post_type = '$type' ";
				$multiple = true;
			}
			$query .= " )\n";
		}
		$query .= "
		ORDER BY {$prefix}posts.post_date DESC ";
		if ( $limit = $this->get_option( 'querylimit' ) )
			$query .= " LIMIT $limit ";
		if ( $offset )
			$query .= " OFFSET $offset ";
		$query .= ';';
		$query = apply_filters( 'better-related-cquery', $query );
		$this->log( $query, 'query' );
		$posts = $wpdb->get_results( $query, ARRAY_N );
		$this->score['queries']++;
		$this->log( print_r( $posts, true ), 'query' );
		// $post[0] is the post ID
		// $post[1] is the mysql relevance score
		if ( $posts ) {
			foreach( $posts as $post ) {
				if ( !isset( $this->score[$id][$post[0]] ) )
					$this->score[$id][$post[0]] = 0;
				$this->score[$id][$post[0]] += $post[1] * $weight;
			}
		}
	}

	/**
	 * Award relatedness points for terms of a single post.
	 *
	 * Get a list of taxonomies the post supports, and get relatedness scores
	 * for every term the post uses.
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Post ID of the post we want to find related posts for
	 * @param int $weight The multiplier to use for relatedness
	 * @return none
	 */
	private function build_score_for_taxonomies( $id, $weight, $offset ) {
		// @fixme this is reduntant!?
		if ( !$this->get_option( 'do_x2x' ) )
			return;
		$post_type			= get_post_type( $id );
		$supported_taxes	= get_post_taxonomies( $id );
		$matches			= array();
		foreach( $supported_taxes as $tax ) {
			$matches			= array();
			$post_terms			= get_the_terms( $id, $tax );
			if ( !is_array( $post_terms ) )
				return;
			$post_terms_count	= count( $post_terms );
			$taxonomy_terms		= get_terms( $tax );
			$tax_terms_count	= count( $taxonomy_terms );
			// find all matching terms and count them
			$related_posts_count= 0;
			foreach( $post_terms as $term ) {
				$related_posts = $this->get_related_posts_by_term(
					$id,
					$tax,
					$term->name,
					$post_type,
					$offset
				);
				$related_posts_count	+= count( $related_posts );
				$matches[$term->name]	= $related_posts;
			}
			// Determine importance of each match in the set.
			// @todo option
			// @todo different factor/weight for different taxonomies
			// this is linear, which leads to matches in small taxonomies being
			// very important. good or bad?
			$factor = 100 / $tax_terms_count * $post_terms_count;
			// At last, apply the scores
			foreach ( $matches as $key => $matches ) {
				foreach ( $matches as $match_id ) {
					if ( !isset( $this->score[$id] ) )
						$this->score[$id] = array();
					if ( !isset( $this->score[$id][$match_id] ) )
						$this->score[$id][$match_id] = 0;
					$this->score[$id][$match_id] += $factor * $weight;
				}
			}
		}
	}

	/**
	 * Get a list of related posts by taxonomy, term and/or post type
	 *
	 * @todo limit by date
	 * @todo multisite, find related content on other blogs
	 * @since 0.0.1
	 *
	 * @param int $id Post ID
	 * @param str $taxonomy the taxonomy
	 * @param str $term the term
	 * @param mixed $post_type the post type as string or a post types array
	 * @return mixed array of post IDs
	 */
	private function get_related_posts_by_term( $id, $taxonomy, $term, $post_type = false, $offset ) {
		global $wpdb;
		$prefix	= $wpdb->prefix;
		$term	= $wpdb->escape( $term );
		$query	= "
		SELECT SQL_CALC_FOUND_ROWS post_name, ID FROM {$prefix}posts
		INNER JOIN {$prefix}term_relationships ON ({$prefix}posts.ID = {$prefix}term_relationships.object_id)
		INNER JOIN {$prefix}term_taxonomy ON ({$prefix}term_relationships.term_taxonomy_id = {$prefix}term_taxonomy.term_taxonomy_id)
		INNER JOIN {$prefix}terms ON ({$prefix}term_taxonomy.term_id = {$prefix}terms.term_id)
		WHERE ID != $id
		";
		// We can not apply different weights to different taxonomies. This
		// doesn't look like an important feature at the moment.
		if ( $this->get_option( 'usetax' ) )
			$taxonomy = array_keys( $this->get_option( 'usetax' ) );
		if ( is_string( $taxonomy ) )
			$query .= " AND {$prefix}term_taxonomy.taxonomy = '$taxonomy' ";
		elseif( is_array( $taxonomy ) ) {
			$query .= " AND ( ";
			$multiple = false;
			foreach( $taxonomy as $tax ) {
				if ( $multiple )
					$query .= ' OR ';
				$query .= " {$prefix}term_taxonomy.taxonomy = '$tax' ";
				$multiple = true;
			}
			$query .= " )\n";
		}
		$query .= " AND {$prefix}terms.name IN ('$term')\n";
		// @todo Wouldn't it be better to find all post types and do the
		// filtering in the display?
		if ( $this->get_option( 'usept' ) )
			$post_type = array_keys( $this->get_option( 'usept' ) );
		if ( is_string( $post_type ) ) {
			$query .= " AND {$prefix}posts.post_type = '$post_type' ";
		}
		elseif( is_array( $post_type ) ) {
			$query .= " AND ( ";
			$multiple = false;
			foreach( $post_type as $type ) {
				if ( $multiple )
					$query .= ' OR ';
				$query .= " {$prefix}posts.post_type = '$type' ";
				$multiple = true;
			}
			$query .= " )\n";
		}
		$query .= "
		AND ({$prefix}posts.post_status = 'publish' OR {$prefix}posts.post_status = 'private')
		GROUP BY {$prefix}posts.ID
		ORDER BY {$prefix}posts.post_date DESC
		";
		if ( $limit = $this->get_option( 'querylimit' ) )
			$query .= " LIMIT $limit ";
		if ( $offset )
			$query .= " OFFSET $offset ";
		$query .= ';';
		$query = apply_filters( 'better-related-taxquery', $query );
		$this->log( $query, 'query' );
		$posts = $wpdb->get_results( $query, OBJECT );
		$this->score['queries']++;
		$r = array();
		foreach( @$posts as $post ) {
			array_push( $r, $post->ID );
		}
		return $r;
	}

	/**
	 * Update score creation timestamp
	 *
	 * @since 0.3.5
	 *
	 * @return none
	 */
	private function timestamp_score() {
		$this->score['ctime'] = time();
	}

	/**
	 * Return timestamp as float with microseconds
	 *
	 * @since 0.3.5
	 *
	 * @return float timestamp
	 */
    private function microtime() {
        $string = explode( ' ', microtime() );
        return intval( $string[1] ) + floatval( $string[0] );
    }

}
