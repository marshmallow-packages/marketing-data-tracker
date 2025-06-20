<script>
    function storeMarketingCookies() {
        // Get all cookies with key & value in a more efficient way
        const cookies = document.cookie ?
            Object.fromEntries(
                document.cookie.split(';').map(cookie => {
                    const [name, ...rest] = cookie.trim().split('=');
                    return [
                        decodeURIComponent(name),
                        decodeURIComponent(rest.join('='))
                    ];
                }).filter(([name, value]) => name && value)
            ) : {};

        // Only send if we have cookies
        if (Object.keys(cookies).length === 0) {
            return;
        }

        console.log('Storing marketing cookies:', cookies);

        // Use fetch API for better performance and modern approach
        fetch("{{ route('marketing-data-tracker.store-marketing-cookies') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    marketing_cookies: cookies
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Marketing cookies stored successfully:', data);
            })
            .catch(error => {
                console.error('Error storing marketing cookies:', error);
            });
    }

    @if (!empty(config('marketing-data-tracker.store_marketing_cookies', [])))
        storeMarketingCookies();
    @endif
</script>
