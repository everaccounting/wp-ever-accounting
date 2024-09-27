<?php
/**
 * Admin List of Categories.
 * Page: Misc
 * Tab: Categories
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $category \EverAccounting\Models\Category Category object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Categories', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-settings&tab=categories&action=add' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
		</a>
		<?php if ( $list_table->get_request_search() ) : ?>
			<span class="subtitle"><?php echo esc_html( sprintf( /* translators: %s: Get requested search string */ __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $list_table->get_request_search() ) ) ); ?></span>
		<?php endif; ?>
	</h1>
	<form id="eac-categories-list-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $list_table->views(); ?>
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $list_table->display(); ?>
		<input type="hidden" name="page" value="eac-settings"/>
		<input type="hidden" name="tab" value="categories"/>
	</form>

<script type="text/html" id="tmpl-eac-category-modal">
	<div class="eac-modal-header">
		<h3><?php esc_html_e( 'Add New Category', 'wp-ever-accounting' ); ?></h3>
	</div>
	<form id="eac-category-modal" class="eac-modal-body">
		<div class="eac-form-field">
			<label for="category-name"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></label>
			<input type="text" id="category-name" name="name" required value="{{ data.name }}" />
		</div>
		<div class="eac-form-field">
			<label for="category-type"><?php esc_html_e( 'Type', 'wp-ever-accounting' ); ?></label>
			<select id="category-type" name="type" required>
				<?php foreach ( EAC()->categories->get_types() as $type => $label ) : ?>
					<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, '{{ data.type }}' ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="eac-form-field">
			<label for="category-description"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
			<textarea id="category-description" name="description">{{ data.description }}</textarea>
		</div>
	</form>
	<div class="eac-modal-footer">
		<button class="button button-primary" form="eac-category-modal"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></button>
		<button class="button" data-eacmodal-close><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
	</div>
</script>
<?php
