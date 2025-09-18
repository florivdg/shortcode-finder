/**
 * Shortcode Finder Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        var $container = $('.shortcode-finder-container');
        var $searchInput = $('#shortcode-input');
        var $searchButton = $('#search-button');
        var $spinner = $('.search-form .spinner');
        var $message = $('#search-message');
        var $resultsSection = $('.results-section');
        var $resultsContainer = $('#results-container');

        // Handle search button click
        $searchButton.on('click', function() {
            performSearch();
        });

        // Handle Enter key in search input
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });

        // Clear message when user starts typing
        $searchInput.on('input', function() {
            if ($message.is(':visible')) {
                $message.slideUp('fast');
            }
        });

        // Perform the search
        function performSearch() {
            var shortcode = $searchInput.val().trim();

            // Validate input
            if (!shortcode) {
                showMessage('Please enter a shortcode to search for.', 'error');
                $searchInput.focus();
                return;
            }

            // Show loading state
            $searchButton.prop('disabled', true);
            $spinner.addClass('is-active');
            $message.slideUp('fast');
            $container.addClass('loading');

            // Perform AJAX request
            $.ajax({
                url: shortcode_finder_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'shortcode_finder_search',
                    shortcode: shortcode,
                    nonce: shortcode_finder_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.html) {
                            // Show results
                            $resultsContainer.html(response.data.html);
                            $resultsSection.addClass('new-results').slideDown('fast');

                            // Remove animation class after animation completes
                            setTimeout(function() {
                                $resultsSection.removeClass('new-results');
                            }, 2000);

                            // Scroll to results
                            $('html, body').animate({
                                scrollTop: $resultsSection.offset().top - 100
                            }, 500);
                        } else {
                            // No results found
                            showMessage(response.data.message, 'warning');
                            $resultsSection.slideUp('fast');
                        }
                    } else {
                        // Error in search
                        showMessage(response.data.message || 'An error occurred during the search.', 'error');
                        $resultsSection.slideUp('fast');
                    }
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to perform search. Please try again.', 'error');
                    console.error('Search error:', error);
                    $resultsSection.slideUp('fast');
                },
                complete: function() {
                    // Reset loading state
                    $searchButton.prop('disabled', false);
                    $spinner.removeClass('is-active');
                    $container.removeClass('loading');
                }
            });
        }

        // Show message to user
        function showMessage(text, type) {
            type = type || 'info';

            // Remove all notice classes and add the appropriate one
            $message
                .removeClass('notice-success notice-error notice-warning notice-info')
                .addClass('notice-' + type);

            // Set the message text and show it
            $message.html('<p>' + text + '</p>').slideDown('fast');

            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(function() {
                    $message.slideUp('fast');
                }, 5000);
            }
        }

        // Initialize focus on page load
        $searchInput.focus();

        // Handle table row hover effect
        $(document).on('mouseenter', '#results-container tbody tr', function() {
            $(this).addClass('hover');
        }).on('mouseleave', '#results-container tbody tr', function() {
            $(this).removeClass('hover');
        });

        // Add keyboard navigation for results
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                $searchInput.focus().select();
            }

            // Escape to clear search
            if (e.key === 'Escape') {
                if ($searchInput.is(':focus')) {
                    $searchInput.val('').blur();
                    $message.slideUp('fast');
                }
            }
        });

        // Add copy shortcode functionality
        $(document).on('click', '.copy-shortcode', function(e) {
            e.preventDefault();
            var shortcode = $(this).data('shortcode');
            copyToClipboard(shortcode);
            showMessage('Shortcode copied to clipboard!', 'success');
        });

        // Copy to clipboard helper
        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            } else {
                // Fallback for older browsers
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
            }
        }
    });

})(jQuery);