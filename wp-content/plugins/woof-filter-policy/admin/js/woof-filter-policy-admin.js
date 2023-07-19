(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery(document).ready(function ($) {
		// Get the original form HTML
		var originalFormHtml = $('#wbc-options').prop('outerHTML');

		// Function to clone the form and make elements unique
		function cloneForm() {
			// Create a new form element from the original form HTML
			var $newForm = $(originalFormHtml);

			// Generate a unique ID for the new form
			var uniqueFormId = 'wbc-options-' + Date.now();
			$newForm.attr('id', uniqueFormId);

			// Update the names of form elements to make them unique
			$newForm.find('select[name^="woof_filter_policy_option_name"]').each(function () {
				var newName = $(this).attr('name').replace('woof_filter_policy_option_name', 'woof_filter_policy_option_name_' + uniqueFormId);
				$(this).attr('name', newName);
			});

			$newForm.find('input[type="checkbox"][name^="woof_filter_policy_option_name"]').each(function () {
				var newName = $(this).attr('name').replace('woof_filter_policy_option_name', 'woof_filter_policy_option_name_' + uniqueFormId);
				$(this).attr('name', newName);

				// Reset the checkboxes in the cloned form
				$(this).prop('checked', false);
			});

			// Hide term blocks in the cloned form
			$newForm.find('.woof-filter-terms-select').hide();
			$newForm.find('.terms-title .toggle-checkboxes').text('▼');

			// Append the new form after the original form
			$('.wbc-col').prepend($newForm);

			// Reinitialize event handlers for the new form
			initializeEventHandlers($newForm);
		}

		// Function to initialize event handlers for a form
		function initializeEventHandlers($form) {
			$form.find('.woof-filters-select input[type="checkbox"]').on('change', function () {
				var taxonomy = $(this).val();
				var $filterTerms = $form.find('.term_wrapper .woof-filter-terms-select[data-taxonomy="' + taxonomy + '"]');
				if ($(this).is(':checked')) {
					$filterTerms.show();
					$filterTerms.prev('h3').show();
				} else {
					$filterTerms.hide();
					$filterTerms.prev('h3').hide();
				}
			});


			// Toggle the checkboxes when clicking on the arrow icon
			$form.find('.terms-title .toggle-checkboxes').on('click', function () {
				$(this).closest('h3').next('.woof-filter-terms-select').toggle();
				$(this).text(function (i, text) {
					return text === '▼' ? '▲' : '▼';
				});
			});

			$form.find('.toggle-filters').on('click', function () {
				$form.find('.woof-filters-select .hidden-checkbox').toggle();
				$(this).text(function (i, text) {
					return text === '▼ Развернуть' ? '▲ Свернуть' : '▼ Развернуть';
				});
			});
		}

		// Initialize event handlers for the original form
		initializeEventHandlers($('#wbc-options'));

		// Button click event to add a new form
		$('.add-form').on('click', function () {
			cloneForm();
		});
	});
})( jQuery );
