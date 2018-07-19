WP Connext
==========

A WordPress plugin that implements and manages Connext

## Available filters

### Post Types
`wp_cxt_meta_box_post_types`

| Parameter | Type | Description | Default
| --- | --- | --- | --- |
| `$post_types` | `array` | Post types in which the WP Connext Meta Box is enabled. | `array( 'post', 'page' )`

#### Example
```
function enabled_connext_post_types( $post_types ) {
	// remove pages as available post types
	$page_key = array_search( 'page', $post_types );
	if ( false !== $page_key ) {
		unset( $post_types[ $page_key ] );
	}
	return $post_types;
}
add_filter( 'wp_cxt_meta_box_post_types', 'enabled_connext_post_types', 10, 1 );
```

### Taxonomies
`wp_cxt_settings_taxonomies`

| Parameter | Type | Description | Default
| --- | --- | --- | --- |
| `$taxonomies` | `array` | Array of Taxonomy objects in which WP Connext is enabled. | All public taxonomies

#### Example
```
function enabled_connext_taxonomies( $taxonomies ) {
	// add a custom private taxonomy to the collection
	$my_private_taxonomy = get_taxonomy( 'my_private_taxonomy' );
	if ( $my_private_taxonomy ) {
		$taxonomies[] = $my_private_taxonomy;
	}
	return $taxonomies;
}
add_filter( 'wp_cxt_settings_taxonomies', 'enabled_connext_taxonomies', 10, 1 );
```

### Public Script Rendering
`wp_cxt_connext_enabled`
By default, the plugin will render the Connext scripts based on the plugin settings as determined when the `wp_enqueue_scripts` hook is fired. This filter allows you to override and customize that logic.

| Parameter | Type | Description | Default
| --- | --- | --- | --- |
| `$connext_enabled` | `bool` | Whether or not Connext should be enabled on this specific page, determined by the plugin settings. | N/A

#### Example
```
function custom_connext_rendering_logic( $connext_enabled ) {
	// allow connext to render on the search page
	if ( is_search() ) {
		return true;
	}
}
add_filter( 'wp_cxt_connext_enabled', 'custom_connext_rendering_logic', 10, 1 );
```
