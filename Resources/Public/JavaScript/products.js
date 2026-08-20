const root = document.querySelector('[data-viewmend-products]');

if (root && root.dataset.ready !== '1') {
  root.dataset.ready = '1';
  const feedback = root.querySelector('[data-copy-feedback]');

  root.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-command]');
    if (!(button instanceof HTMLButtonElement)) {
      return;
    }

    const command = button.dataset.command || '';
    if (!command) {
      return;
    }

    try {
      await navigator.clipboard.writeText(command);
      button.textContent = 'Copied';
      if (feedback) {
        feedback.textContent = `${button.dataset.product || 'Product'} installation command copied.`;
      }
      window.setTimeout(() => {
        button.textContent = 'Copy install command';
      }, 1800);
    } catch (_error) {
      const input = button.closest('[data-product-row]')?.querySelector('[data-install-command]');
      if (input instanceof HTMLInputElement) {
        input.focus();
        input.select();
      }
      if (feedback) {
        feedback.textContent = 'Copy was unavailable. The command has been selected for you.';
      }
    }
  });
}
