export function mountShareSheet(root) {
    const url = root.dataset.shareUrl;
    const title = root.dataset.shareTitle;
    const text = root.dataset.shareText;
    const nativeButton = root.querySelector('[data-share-native]');
    const copyButton = root.querySelector('[data-share-copy]');
    const status = root.querySelector('[data-share-status]');

    const markCopied = () => {
        if (!status) {
            return;
        }
        status.textContent = 'Lien copié';
        status.classList.remove('hidden');
    };

    const copyLink = async () => {
        try {
            await navigator.clipboard.writeText(url);
            markCopied();
        } catch {
            markCopied();
        }
    };

    nativeButton?.addEventListener('click', async () => {
        if (navigator.share) {
            try {
                await navigator.share({ title, text, url });
                return;
            } catch (error) {
                if (error?.name === 'AbortError') {
                    return;
                }
            }
        }

        await copyLink();
    });

    copyButton?.addEventListener('click', copyLink);
}
