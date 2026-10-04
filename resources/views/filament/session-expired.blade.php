{{--
    A lapsed session (HTTP 419) on a Livewire request, in the admin panel.

    Livewire's own answer is a browser dialog ("This page has expired. Would you
    like to refresh the page?"). Here the page just reloads: someone who has
    been signed out lands on the login page, someone still signed in gets a
    fresh page and token. A short guard stops a reload loop if the second
    request fails the same way.
--}}
<script>
    document.addEventListener('livewire:init', () => {
        window.Livewire.interceptRequest(({ onError }) => {
            onError(({ response, preventDefault }) => {
                if (response.status !== 419) {
                    return;
                }

                preventDefault();

                try {
                    const last = Number(sessionStorage.getItem('baardy.expiredReload') || 0);

                    if (Date.now() - last < 5000) {
                        return;
                    }

                    sessionStorage.setItem('baardy.expiredReload', String(Date.now()));
                } catch (error) {
                    // Storage can be unavailable; reload anyway.
                }

                window.location.reload();
            });
        });
    });
</script>
