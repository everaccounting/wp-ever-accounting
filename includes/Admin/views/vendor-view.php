<?php
/**
 * Admin View: Vendor details.
 *
 * @package EverAccounting
 * @subpackage EverAccounting/Admin/Views
 * @since 1.0.0
 *
 * @var $vendor \EverAccounting\Models\Vendor Vendor object.
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="eac-profile-header">
	<div class="eac-profile-header__avatar">
		<?php echo get_avatar( $vendor->email, 120 ); ?>
	</div>

	<div class="eac-profile-header__columns tw-align-center tw-justify-center">
		<div class="eac-profile-header__column">
			<div class="eac-profile-header__title">Tremblay and Rath</div>
			<p>XYZ Company</p>
			<p>22, Ave Street, Newyork, USA</p>
		</div>
	</div>
</div>

<div class="eac-poststuff is--alt">
	<div class="column-1">
		<ul class="eac-profile-nav" role="tablist">
			<li class="nav-item" role="presentation">
				<a href="#" data-bs-toggle="tab" data-bs-target="#activities" class="nav-link active" aria-selected="true" role="tab"><i class="ti ti-alarm-minus me-1"></i>Activities</a>
			</li>
			<li class="nav-item" role="presentation">
				<a href="#" data-bs-toggle="tab" data-bs-target="#notes" class="nav-link" aria-selected="false" role="tab" tabindex="-1"><i class="ti ti-notes me-1"></i>Notes</a>
			</li>
			<li class="nav-item" role="presentation">
				<a href="#" data-bs-toggle="tab" data-bs-target="#calls" class="nav-link" aria-selected="false" role="tab" tabindex="-1"><i class="ti ti-phone me-1"></i>Calls</a>
			</li>
			<li class="nav-item" role="presentation">
				<a href="#" data-bs-toggle="tab" data-bs-target="#files" class="nav-link" aria-selected="false" tabindex="-1" role="tab"><i class="ti ti-file me-1"></i>Files</a>
			</li>
			<li class="nav-item" role="presentation">
				<a href="#" data-bs-toggle="tab" data-bs-target="#email" class="nav-link" aria-selected="false" tabindex="-1" role="tab"><i class="ti ti-mail-check me-1"></i>Email</a>
			</li>
		</ul>
	</div>
	<div class="column-2">
		<div class="eac-list has--border has--split">
			<div class="eac-list__header">
				<h3 class="eac-list__title">Vendor Information</h3>
			</div>
			<div class="eac-list__item">
				<div class="eac-list__label">Name</div>
				<div class="eac-list__value">Tremblay and Rath</div>
			</div>
			<div class="eac-list__item">
				<div class="eac-list__label">Email</div>
				<div class="eac-list__value"> [email protected]</div>
			</div>
		</div>
	</div>
</div>
