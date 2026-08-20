const root = document.querySelector('[data-viewmend-products]');

if (root && root.dataset.ready !== '1') {
  root.dataset.ready = '1';

  const filterButtons = [...root.querySelectorAll('[data-product-filter]')];
  const productGroups = [...root.querySelectorAll('[data-product-group]')];
  const productItems = [...root.querySelectorAll('[data-product-item]')];
  const noResults = root.querySelector('[data-products-no-results]');
  const dialog = root.querySelector('[data-install-dialog]');
  const dialogTitle = root.querySelector('[data-install-title]');
  const command = root.querySelector('[data-install-command-text]');
  const copyFeedback = root.querySelector('[data-copy-feedback]');
  let dialogTrigger = null;

  const applyFilter = (status) => {
    filterButtons.forEach((button) => {
      const selected = button.dataset.productFilter === status;
      button.classList.toggle('is-active', selected);
      button.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });

    productItems.forEach((item) => {
      item.hidden = status !== 'all' && item.dataset.productStatus !== status;
    });

    productGroups.forEach((group) => {
      group.hidden = status !== 'all' && group.dataset.productGroup !== status;
    });

    if (noResults instanceof HTMLElement) {
      const visibleItem = productItems.some((item) => !item.hidden && !item.closest('[data-product-group]')?.hidden);
      const visibleEmptyState = [...root.querySelectorAll('[data-product-empty-state]')]
        .some((state) => !state.closest('[data-product-group]')?.hidden);
      noResults.hidden = visibleItem || visibleEmptyState;
    }
  };

  const closeDialog = () => {
    if (!(dialog instanceof HTMLDialogElement) || !dialog.open) {
      return;
    }
    dialog.close();
    if (dialogTrigger instanceof HTMLElement) {
      dialogTrigger.focus();
    }
    dialogTrigger = null;
  };

  root.addEventListener('click', async (event) => {
    if (!(event.target instanceof Element)) {
      return;
    }

    const filterButton = event.target.closest('[data-product-filter]');
    if (filterButton instanceof HTMLButtonElement) {
      applyFilter(filterButton.dataset.productFilter || 'all');
      return;
    }

    const installButton = event.target.closest('[data-open-install]');
    if (installButton instanceof HTMLButtonElement && dialog instanceof HTMLDialogElement) {
      dialogTrigger = installButton;
      if (dialogTitle instanceof HTMLElement) {
        dialogTitle.textContent = `Install ${installButton.dataset.productTitle || 'product'}`;
      }
      if (command instanceof HTMLElement) {
        command.textContent = installButton.dataset.installCommand || '';
      }
      dialog.showModal();
      return;
    }

    if (event.target.closest('[data-close-install]')) {
      closeDialog();
      return;
    }

    const copyButton = event.target.closest('[data-copy-install]');
    if (!(copyButton instanceof HTMLButtonElement) || !(command instanceof HTMLElement)) {
      return;
    }

    const value = command.textContent || '';
    try {
      await navigator.clipboard.writeText(value);
      if (copyFeedback) {
        copyFeedback.textContent = 'Composer command copied.';
      }
    } catch (_error) {
      const selection = window.getSelection();
      const range = document.createRange();
      range.selectNodeContents(command);
      selection?.removeAllRanges();
      selection?.addRange(range);
      if (copyFeedback) {
        copyFeedback.textContent = 'Copy was unavailable. The Composer command has been selected.';
      }
    }
  });

  if (dialog instanceof HTMLDialogElement) {
    dialog.addEventListener('click', (event) => {
      if (event.target !== dialog) {
        return;
      }
      const bounds = dialog.getBoundingClientRect();
      const inside = event.clientX >= bounds.left
        && event.clientX <= bounds.right
        && event.clientY >= bounds.top
        && event.clientY <= bounds.bottom;
      if (!inside) {
        closeDialog();
      }
    });
  }
}
