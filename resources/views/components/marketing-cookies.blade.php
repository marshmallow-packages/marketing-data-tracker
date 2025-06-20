<script>
    function storeMarketingCookies() {
        var data = new FormData();

        // Get all cookies with key & value
        var cookies = {};
        if (document.cookie && document.cookie !== '') {
            var cookieArray = document.cookie.split(';');
            for (var i = 0; i < cookieArray.length; i++) {
                var cookie = cookieArray[i].trim();
                var cookieParts = cookie.split('=');
                if (cookieParts.length === 2) {
                    var cookieName = decodeURIComponent(cookieParts[0]);
                    var cookieValue = decodeURIComponent(cookieParts[1]);
                    cookies[cookieName] = cookieValue;
                }
            }
        }

        // Send all cookies as JSON
        data.append('marketing_cookies', JSON.stringify(cookies));

        console.log(cookies);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'store-marketing-cookies', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(data);
    }

    @if (!empty(config('marketing-data-tracker.store_marketing_cookies', [])))
        storeMarketingCookies();
    @endif
</script>
