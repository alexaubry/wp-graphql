<?php
/**
 * Class SampleTest
 *
 * @package Wp_Graphql
 */

use \BEForever\WPGraphQL;
use \BEForever\WPGraphQL\TypeSystem;
use \BEForever\WPGraphQL\AppContext;
use \BEForever\WPGraphQL\Data\DataSource;
use \GraphQL\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Type\Definition\Config;
use \GraphQL\Error\FormattedError;

/**
 * Sample test case.
 */
class Query_Test extends WP_UnitTestCase {

	/**
	 * Tests the query for hello.
	 */
	public function test_hello_query() {
		$query = '{hello}';
		$expected = array(
			'data' => array(
				'hello' => 'Welcome to WP GraphQL, I hope that you will enjoy this adventure!',
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post.
	 */
	public function test_post_query() {
		$post_args = array(
			'post_status' => 'publish',
			'post_content' => 'Hi!',
			'post_title' => 'Hello!',
		);

		$post_id = $this->factory->post->create( $post_args );

		$query = "{ post(id: {$post_id}) { content, title, author } }";
		$expected = array(
			'data' => array(
				'post' => array(
					'content' => 'Hi!',
					'title' => 'Hello!',
					'author' => '0',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for post fields.
	 */
	public function test_post_introspection_fields() {
		$query = '{__type(name: "Post") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'author' ),
						array( 'name' => 'date' ),
						array( 'name' => 'date_gmt' ),
						array( 'name' => 'content' ),
						array( 'name' => 'title' ),
						array( 'name' => 'excerpt' ),
						array( 'name' => 'post_status' ),
						array( 'name' => 'comment_status' ),
						array( 'name' => 'ping_status' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'to_ping' ),
						array( 'name' => 'pinged' ),
						array( 'name' => 'modified' ),
						array( 'name' => 'modified_gmt' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'guid' ),
						array( 'name' => 'menu_order' ),
						array( 'name' => 'type' ),
						array( 'name' => 'mime_type' ),
						array( 'name' => 'comment_count' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for comment.
	 */
	public function test_comment_query() {
		$comment_args = array(
			'comment_approved' => '1',
			'comment_content' => 'Hi!',
		);

		$comment_id = $this->factory->comment->create( $comment_args );

		$query = "{ comment(id: {$comment_id}) { content, approved } }";
		$expected = array(
			'data' => array(
				'comment' => array(
					'content' => 'Hi!',
					'approved' => '1',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the fields schema for comments.
	 */
	public function test_comment_introspection_fields() {
		$query = '{__type(name: "Comment") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'post' ),
						array( 'name' => 'author' ),
						array( 'name' => 'author_ip' ),
						array( 'name' => 'date' ),
						array( 'name' => 'date_gmt' ),
						array( 'name' => 'content' ),
						array( 'name' => 'karma' ),
						array( 'name' => 'approved' ),
						array( 'name' => 'agent' ),
						array( 'name' => 'type' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'user_id' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for user.
	 */
	public function test_user_query() {
		$user_args = array(
			'role'       => 'editor',
			'user_email' => 'graphqliscool@withwp.luv',
		);

		$user_id = $this->factory->user->create( $user_args );

		$query = "{ user(id: {$user_id}) { email } }";
		$expected = array(
			'data' => array(
				'user' => array(
					'email' => 'graphqliscool@withwp.luv',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for user fields.
	 */
	public function test_user_introspection_fields() {
		$query = '{__type(name: "User") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'capabilities' ),
						array( 'name' => 'cap_key' ),
						array( 'name' => 'roles' ),
						array( 'name' => 'extra_capabilities' ),
						array( 'name' => 'email' ),
						array( 'name' => 'first_name' ),
						array( 'name' => 'last_name' ),
						array( 'name' => 'description' ),
						array( 'name' => 'username' ),
						array( 'name' => 'name' ),
						array( 'name' => 'registered_date' ),
						array( 'name' => 'nickname' ),
						array( 'name' => 'url' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'locale' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for term.
	 */
	public function test_term_query() {
		$term_args = array(
			'taxonomy' => 'category',
			'name'     => 'Test',
		);

		$term_id = $this->factory->term->create( $term_args );

		$query = "{ term(id: {$term_id}) { taxonomy, name } }";
		$expected = array(
			'data' => array(
				'term' => array(
					'taxonomy' => 'category',
					'name'     => 'Test',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for term fields.
	 */
	public function test_term_introspection_fields() {
		$query = '{__type(name: "Term") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'group' ),
						array( 'name' => 'taxonomy_id' ),
						array( 'name' => 'taxonomy' ),
						array( 'name' => 'description' ),
						array( 'name' => 'parent' ),
						array( 'name' => 'count' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu item.
	 */
	public function test_menu_item_query() {
		$menu_item_args = array(
			'post_type' => 'nav_menu_item',
			'post_title' => 'Let\'s hope this works!',
		);

		$post_args = array(
			'post_title' => 'Let\'s hope this works!',
		);

		// Nav menu items for whatever reason are posts.
		$menu_item_id = $this->factory->post->create( $menu_item_args );

		$post_id = $this->factory->post->create( $post_args );

		// Match the nav menu Item to a post.
		update_post_meta( $menu_item_id, '_menu_item_object_id', $post_id );

		$query = "{ menu_item(id: {$menu_item_id}) { title, object_id } }";
		$expected = array(
			'data' => array(
				'menu_item' => array(
					'title' => 'Let\'s hope this works!',
					'object_id' => "{$post_id}",
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu item fields.
	 */
	public function test_menu_item_introspection_fields() {
		$query = '{__type(name: "MenuItem") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'title' ),
						array( 'name' => 'type' ),
						array( 'name' => 'object_id' ),
						array( 'name' => 'object' ),
						array( 'name' => 'target' ),
						array( 'name' => 'xfn' ),
						array( 'name' => 'url' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu.
	 */
	public function test_menu_query() {
		$menu_args = array(
			'taxonomy' => 'nav_menu',
			'name' => 'Test Menu',
			'slug' => 'test-menu',
		);

		// Nav menu items for whatever reason are posts.
		$menu_id = $this->factory->term->create( $menu_args );

		$query = "{ menu(id: {$menu_id}) { id, name, slug, group } }";
		$expected = array(
			'data' => array(
				'menu' => array(
					'id' => "{$menu_id}",
					'name' => 'Test Menu',
					'slug' => 'test-menu',
					'group' => '0',
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu fields.
	 */
	public function test_menu_introspection_fields() {
		$query = '{__type(name: "Menu") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'id' ),
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
						array( 'name' => 'group' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu location.
	 */
	public function test_menu_location_query() {
		$registered_menus = get_registered_nav_menus();
		$slug = key( $registered_menus );
		$name = current( $registered_menus );

		$query = "{ menu_location(slug: \"{$slug}\") { name, slug } }";
		$expected = array(
			'data' => array(
				'menu_location' => array(
					'name' => $name,
					'slug' => $slug,
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for menu location fields.
	 */
	public function test_menu_location_introspection_fields() {
		$query = '{__type(name: "MenuLocation") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'name' ),
						array( 'name' => 'slug' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for theme.
	 */
	public function test_theme_query() {
		$slug = 'twentyfifteen';
		$theme = wp_get_theme( $slug );

		$query = "{ theme(slug: \"{$slug}\") { name, author, slug } }";
		$expected = array(
			'data' => array(
				'theme' => array(
					'name'   => $theme->get( 'Name' ),
					'author' => $theme->get( 'Author' ),
					'slug'   => $theme->get_stylesheet(),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests the query for theme fields.
	 */
	public function test_theme_introspection_fields() {
		$query = '{__type(name: "Theme") {fields {name}}}';
		$expected = array(
			'data' => array(
				'__type' => array(
					'fields' => array(
						array( 'name' => 'slug' ),
						array( 'name' => 'name' ),
						array( 'name' => 'screenshot' ),
						array( 'name' => 'theme_uri' ),
						array( 'name' => 'description' ),
						array( 'name' => 'author' ),
						array( 'name' => 'author_uri' ),
						array( 'name' => 'tags' ),
						array( 'name' => 'version' ),
					),
				),
			),
		);

		$this->check_graphql_response( $query, $expected );
	}

	/**
	 * Tests expected results against response from GraphQL query.
	 *
	 * @param string $query    GraphQL query string.
	 * @param mixed  $expected Expected data to be returned in response.
	 */
	private function check_graphql_response( $query, $expected ) {
		// Build the complete type system.
		$type_system = new TypeSystem();

		// Build request context that will be available in all field resolvers (as 3rd argument).
		$app_context = new AppContext();

		// Build GraphQL schema out of the query object type.
		$schema = new Schema([
			'query' => $type_system->query(),
		]);

		$data = array();
		$data['query'] = $query;
		$data['variables'] = null;

		// Execute the query.
		$result = GraphQL::execute(
			$schema,
			$data['query'],
			null,
			$app_context,
			(array) $data['variables'],
			null
		);

		$this->assertEquals( $result, $expected );
	}
}
