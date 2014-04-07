<?php

/**
 * @package better-related
 * @subpackage frontend
 * @since 0.1
 */
class BetterRelatedFrontend extends BetterRelated {

	/**
	 * Constructor, set up the frontend
	 *
	 * @since 0.0.1
	 *
	 * @return none
	 */
	public function __construct() {
		BetterRelated::__construct();
		add_filter(
			'the_content',
			array( $this, 'auto_add' ),
			$this->get_option( 'filterpriority' )
		);
		add_filter(
			'better_related_sort_score',
			array( $this, 'filter_score' ),
			10
		);
		if ( $this->get_option( 'stylesheet' ) )
			add_action( 'wp_print_styles', array( $this, 'css' ) );
		#add_action( '{$prefix}footer', array( $this, 'debug' ) );
	}

	/**
	 * Include our default css
	 *
	 * @since 0.3.5
	 *
	 * @return none
	 */
	public function css() {
		wp_register_style(
			'better-related-frontend',
			plugins_url( basename( $this->plugin_dir ) . '/css/better-related.css' ),
			null,
			'0.3.5'
		);
		wp_enqueue_style( 'better-related-frontend' );
	}

	/**
	 * Tempate tag helper, echos HTML markup of related posts
	 *
	 * @todo document: as we can override the config, consecutive calls of
	 *   the_related() keep the new config
	 * @todo this works without parameter (?), why?
	 * @since 0.0.1
	 *
	 * @param integer $id post ID
	 * @param array $config config to use for this and the scorer object
	 */
	public function the_related( $id, $config = null ) {
		if ( isset( $config ) )
			$this->override_options( $config );
		echo $this->get_the_related( $id, $config );
	}

	/**
	 * Template tag helper, returns scores for a post
	 *
	 * @since 0.3.7
	 *
	 * @param int $id Post ID
	 * @param array $config config to use for this and the scorer object
	 * @return array Scores for a post
	 */
	public function get_the_scores( $id, $config = null ) {
		$scores = $this->get_score_for_post( $id, $config );
		return $scores[$id];
	}

	/**
	 * Tempate tag helper, returns HTML markup of related posts
	 *
	 * @since 0.2.2
	 *
	 * @param integer $id post ID
	 * @param array $config config to use for this and the scorer object
	 * @return string HTML markup
	 */
	private function get_the_related( $id, $config = null ) {
		$scores = $this->get_score_for_post( $id, $config );
		if ( isset( $scores[$id] ) ) {
			$queries	= $scores['queries'];
			$qtime		= $scores['etime'] - $scores['stime'];
			$offset		= $scores['offset'];
			$scores		= apply_filters( 'better_related_sort_score', $scores[$id] );
		}
		$related = '';
		if ( $scores && count( $scores ) ) {
			$post_types	= $this->get_option( 'usept' );
			if ( !is_array( $post_types ) )
				$post_types = array( get_post_type() );
			else
				$post_types = array_keys( $post_types );
			$listed = 0;
			foreach( $scores as $id => $score ) {
				if ( $score > $this->get_option( 'minscore' ) ) {
					if ( $listed >= $this->get_option( 'maxresults' ) )
						break;
					$listed++;
					$link			= get_permalink( $id );
					$title			= get_the_title( $id );
					$post_type		= get_post_type( $id );
					if ( !in_array( $post_type, $post_types ) )
						continue;
					if ( !$title ) {
						$title = __(
							'This entry has no title',
							'better-related'
						);
					}
					$description	= $title;
					$showscore		= '';
					if ( current_user_can( 'manage_options' ) && $this->get_option( 'showdetails' ) ) {
						$showscore = sprintf(
							"<span class=\"score\">(%.3f)</span>",
							$score
						);
					}
					$related .= "<li> <a href=\"$link\" title=\"Permanent link to $description\">$title</a> $showscore </li>\n";
				}
			}
		}
		if ( $related ) {
			// thanks link
			$atitle = __( 'Related content found by the Better Related Posts plugin', 'better-related' );
			$ahref = 'http://www.nkuttler.de/wordpress-plugin/wordpress-related-posts-plugin/';
			// @todo markup should probably be configurable somehow
			$pre = '<div class="betterrelated">';
			if ( $relatedtitle = $this->get_option( 'relatedtitle' ) ) {
				$pre .= '<p>';
				$pre .= $relatedtitle;
				if ( $this->get_option( 'thanks' ) == 'info' ) {
					$pre .= '<sup><a class="thanks" style="text-decoration: none;" href="' . $ahref . '" title="' . $atitle . '">?</a></sup>';
				}
				if ( current_user_can( 'manage_options' ) && $this->get_option( 'showdetails' ) ) {
					$pre .= sprintf(
						__( "<span class=\"score\">%s queries in %1.4f seconds, current offset %s</span>", 'better-related' ),
						$queries,
						$qtime,
						$offset
					);
				}
				$pre .= "</p>\n";
			}
			$r = $pre . '<ol>' . $related . '</ol>';
			if ( $this->get_option( 'thanks' ) == 'below' ) {
				$anchor = __( 'Better Related Posts Plugin', 'better-related' );
				$r .= '<a class="thanks" style="font-size: smaller; text-decoration: none;" title="' . $atitle . '" href="' . $ahref . '">' . $anchor  . '</a>';
			}
			$r .= '</div>';
		}
		elseif ( $relatednone = $this->get_option( 'relatednone' ) ) {
			$r = '<div class="betterrelated none">';
			$r .= $relatednone;
			$r .= '</div>';
		}
		return $r;
	}

	/**
	 * Return a score set
	 *
	 * @since 0.3.6
	 *
	 * @param int $id Post ID
	 * @param array $config different config to use for this and the scorer object
	 * @return array Scores for a post
	 */
	private function get_score_for_post( $id, $config = null ) {
		require_once( 'scorer.php' );
		$BetterRelatedScorer = new BetterRelatedScorer( $config );
		$scores = $BetterRelatedScorer->get_score_for_post( $id );
		if ( isset( $scores[$id] ) ) {
			$scores[$id]	= apply_filters(
				'better_related_sort_score',
				$scores[$id]
			);
			return $scores;
		}
		return false;
	}

	/**
	 * Return a score set for a string
	 *
	 * @since 0.4.2
	 *
	 * @param string $string search string
	 * @param array $config different config to use for this and the scorer object
	 * @return array Scores for a post
	 */
	public function get_score_for_string( $string, $config = null ) {
		require_once( 'scorer.php' );
		$BetterRelatedScorer = new BetterRelatedScorer( $config );
		$scores = $BetterRelatedScorer->get_score_for_string( $string );
		if ( isset( $scores[0] ) ) {
			$scores[0]	= apply_filters(
				'better_related_sort_score',
				$scores[0]
			);
			return $scores[0];
		}
		return false;
	}

	/**
	 * Filter that automatically adds a related content list to post contents.
	 *
	 * @since 0.2.3
	 * @todo insert at the top (?!)
	 *
	 * @param string $content content
	 * @return string $content content
	 */
	public function auto_add( $content ) {
		$post_types = $this->get_option( 'autoshowpt' );
		if ( is_feed() && $this->get_option( 'autoshowrss' ) )
			$content = $content . $this->get_the_related( get_the_ID() );
		elseif ( !is_single() )
			return $content;
		elseif ( is_array( $post_types ) )
			foreach ( $post_types as $post_type => $value )
				if ( get_post_type() == $post_type && $value )
					$content = $content . $this->get_the_related( get_the_ID() );
		return $content;
	}

	/**
	 * Filter the scores before we display them. Used by the plugin for sorting
	 *
	 * The scores are always ordered by relevance, however it is possible to
	 * ignore the order through custom loops.
	 *
	 * @todo convert manual scores to floats
	 * @since 0.0.2
	 *
	 * @param array $score A score array
	 * @return array The sorted scores
	 */
	public function filter_score( $score ) {
		if ( !is_array( $score) )
			return false;
		//$score = array_filter( $score, array( $this, 'minscore' ) );
		arsort( $score );
		//$maxresults	= $this->get_option( 'maxresults' );
		//$score = array_slice( $score, 0, $maxresults, true );
		return $score;
	}

	/**
	 * Array filter helper, only let minimum score through.
	 *
	 * @since 0.0.2
	 *
	 * @param integer $score Relatedness score
	 * @return boolean Minimum score reached
	 */
	private function minscore( $score ) {
		$minscore	= $this->get_option( 'minscore' );;
		if ( $score > $minscore )
			return true;
		return false;
	}

	/**
	 * Debug queries, set define('SAVEQUERIES', true);
	 *
	 * @since 0.0.1
	 *
	 * @return none
	 */
	private function debug() {
    	global $wpdb;
		foreach( $wpdb->queries as $query )
			echo '<input size=150 value="' . $query[0] . '"><br>';
	}

}

/**
 * Template tag
 *
 * @since unknown
 *
 * @param interger $id post id
 * @param array $config different config to pass to the scorer object
 * @return none
 */
if ( !function_exists( 'the_related' ) ) {
	function the_related( $id = null, $config = null ) {
		if ( !isset( $id ) || !is_integer( $id ) )
			$id = get_the_ID();
		global $BetterRelatedFrontend;
		$BetterRelatedFrontend->the_related( $id, $config );
	}
}

/**
 * Template tag that returns the scores
 *
 * @since 0.3.6
 *
 * @param interger $id post id
 * @param array $config different config to pass to the scorer object
 * @return array A score set
 */
if ( !function_exists( 'the_related_get_scores' ) ) {
	function the_related_get_scores( $id = null, $config = null ) {
		if ( !isset( $id ) || !is_integer( $id ) )
			$id = get_the_ID();
		global $BetterRelatedFrontend;
		return $BetterRelatedFrontend->get_the_scores( $id, $config );
	}
}

/**
 * Template tag to analyze the various scoring methods
 *
 * @since 0.3.1
 *
 * @param $minscore Minimum score to show
 * @param $maxresults Maximum results to show
 * @return none
 */
if ( !function_exists( 'the_related_analyze' ) ) {
	function the_related_analyze( $minscore = 0, $maxresults = 10 ) {
		echo '<hr><strong>content to content</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 0,
				'do_t2c'		=> 0,
				'do_c2c'		=> 1,
				'do_k2c'		=> 0,
				'do_k2t'		=> 0,
				'do_x2x'		=> 0,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd1-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true,
		    )
		);
		echo '<hr><strong>title to content</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 0,
				'do_t2c'		=> 1,
				'do_c2c'		=> 0,
				'do_k2c'		=> 0,
				'do_k2t'		=> 0,
				'do_x2x'		=> 0,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd2-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true
		    )
		);
		echo '<hr><strong>keywords to content</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 0,
				'do_t2c'		=> 0,
				'do_c2c'		=> 0,
				'do_k2c'		=> 1,
				'do_k2t'		=> 0,
				'do_x2x'		=> 0,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd3-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true
		    )
		);
		echo '<hr><strong>title to title</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 1,
				'do_t2c'		=> 0,
				'do_c2c'		=> 0,
				'do_k2c'		=> 0,
				'do_k2t'		=> 0,
				'do_x2x'		=> 0,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd6-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true
		    )
		);
		echo '<hr><strong>keywords to title</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 0,
				'do_t2c'		=> 0,
				'do_c2c'		=> 0,
				'do_k2c'		=> 0,
				'do_k2t'		=> 1,
				'do_x2x'		=> 0,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd5-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true
		    )
		);
		echo '<hr><strong>terms to taxonomies</strong>';
		the_related(
			get_the_ID(),
		    array(
				'do_t2t'		=> 0,
				'do_t2c'		=> 0,
				'do_c2c'		=> 0,
				'do_k2c'		=> 0,
				'do_k2t'		=> 0,
				'do_x2x'		=> 1,
		        'storage'    	=> 'transient',
		        'storage_id'    => 'd4-',
				'cachetime'		=> 1,
				'minscore'		=> $minscore,
				'maxresults'	=> $maxresults,
				'thanks'		=> false,
				'showdetails'	=> true
		    )
		);
	}
}

/**
 * Template tag, find posts related to a string
 *
 * @since 0.4.2
 *
 * @param string $string search string
 * @return array A score set
 */
if ( !function_exists( 'get_the_related_for_string' ) ) {
	function get_the_related_for_string( $string, $config = null ) {
		global $BetterRelatedFrontend;
		return $BetterRelatedFrontend->get_score_for_string( $string, $config );
	}
}

/**
 * Template tag, list posts related to a string
 *
 * @since 0.4.2
 * @todo should the output be moved into the class to be consistent?
 *
 * @param string $string search string
 * @return array A score set
 */
if ( !function_exists( 'the_related_for_string' ) ) {
	function the_related_for_string( $string, $maxresults = 5, $config = null ) {
		$scores	= get_the_related_for_string( $string, $config );
		$listed = 0;
		foreach( $scores as $id => $score ) {
			if ( $listed >= $maxresults )
				break;
			if ( $score == 0 )
				break;
			$listed++;
			$link			= get_permalink( $id );
			$title			= get_the_title( $id );
			$post_type		= get_post_type( $id );
			if ( !$title ) {
				$title = __(
					'This entry has no title',
					'better-related'
				);
			}
			$description	= $title;
			$related .= "<li> <a href=\"$link\" title=\"Permanent link to $description\">$title</a></li>\n";
		}
		if ( @$related )
			echo '<ul>' . $related . '</ul>';
		elseif ( $relatednone = $config['relatednone'] )
			echo $relatednone;
	}
}
