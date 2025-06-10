(function(){
    'use strict';

    // Configuration from WordPress
    const config = window.shcData || {};
    const endpoint = config.endpoint || '';
    const nonce = config.nonce || '';
    const debug = config.debug || false;

    /**
     * Collect visible links from the page
     * @returns {Array} Array of visible link URLs
     */
    function collectLinks() {
        const anchors = document.querySelectorAll('a');
        const links = [];
        const viewportHeight = window.innerHeight;

        anchors.forEach(anchor => {
            if (!anchor.href) return;

            const rect = anchor.getBoundingClientRect();
            if (rect.top >= 0 && rect.top < viewportHeight) {
                links.push({
                    url: anchor.href,
                    text: anchor.textContent.trim(),
                    position: rect.top
                });
            }
        });

        return links;
    }

    /**
     * Send data to the endpoint
     * @param {string} endpoint - The API endpoint
     * @param {Object} data - The data to send
     */
    function sendData(endpoint, data) {
        if (!endpoint) {
            if (debug) console.error('Hyperlink crawler: No endpoint provided');
            return;
        }

        const payload = JSON.stringify({
            links: data.links,
            width: window.innerWidth,
            height: window.innerHeight,
            timestamp: new Date().toISOString()
        });

        try {
            if (navigator.sendBeacon) {
                const success = navigator.sendBeacon(endpoint, payload);
                if (!success && debug) {
                    console.error('Hyperlink crawler: Beacon failed to send');
                }
            } else {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', endpoint, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-WP-Nonce', nonce);

                xhr.onerror = function() {
                    if (debug) console.error('Hyperlink crawler: XHR request failed');
                };

                xhr.send(payload);
            }
        } catch (e) {
            if (debug) console.error('Hyperlink crawler error:', e);
        }
    }

    // Initial collection on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (!endpoint) return;

        sendData(endpoint, { links: collectLinks() });
    });

    // Re-collect on scroll with debounce for below the fold links
    // let scrollTimeout;
    // window.addEventListener('scroll', function() {
    //     clearTimeout(scrollTimeout);
    //     scrollTimeout = setTimeout(function() {
    //         if (!endpoint) return;
    //         sendData(endpoint, { links: collectLinks() });
    //     }, 250);
    // });

	// Expose internals for testing when running under a test environment.
	if (typeof window !== 'undefined') {
		window.shcCrawlerTest = {
			collectLinks,
			sendData,
		};
	}
})();
