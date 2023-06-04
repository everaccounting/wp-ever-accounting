<?php
/**
 * View: Admin Overview
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="eac-page-section">
	<h2><?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?></h2>
</div>

<div class="eac-summaries-section">
	<ul class="eac-summaries">
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Total Incomes', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_money( eac_get_income_summary()['total'] ) ); ?></div>
			</div>
		</li>
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Total Expenses', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_money( eac_get_expense_summary()['total'] ) ); ?></div>
			</div>
		</li>
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_money( eac_get_profit_summary()['total'] ) ); ?></div>
			</div>
		</li>
	</ul>
</div>

<div class="eac-card">
	<div class="eac-card__header"><?php esc_html_e( 'Cash Flow', 'wp-ever-accounting' ); ?></div>
	<div class="eac-card__body">
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
	</div>
</div>

<div class="eac-columns">
	<div class="eac-col-6">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Profit & Loss', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
	<div class="eac-col-6">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Expenses By Category', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
</div>
<div class="eac-columns">
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Recent Incomes', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
</div>
